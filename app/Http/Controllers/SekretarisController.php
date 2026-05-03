<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Holiday;

class SekretarisController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        
        // Ambil semua siswa aktif beserta profil student (kelas)
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->with('student')
                        ->orderBy('name')
                        ->get();
        
        // Ambil absensi hari ini
        $todayAttendances = Attendance::where('date', $today)
                                      ->with('student')
                                      ->get()
                                      ->keyBy('student_id');
        
        // Cek hari libur (dipindahkan ke atas agar bisa digunakan dalam statistik)
        $holiday = Holiday::where('date', $today)->first();
        
        // Pastikan semua siswa punya record absensi hari ini (create jika belum ada)
        foreach ($students as $student) {
            if (!$todayAttendances->has($student->id)) {
                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'date' => $today,
                    'status' => 'belum_absen',
                    'attendance_time' => null,
                    'created_by' => auth()->id()
                ]);
                $todayAttendances->put($student->id, $attendance);
            }
        }
        
        // Hitung statistik (exclude holidays from 'belum_absen' count)
        $stats = [
            'total' => $students->count(),
            'hadir' => $todayAttendances->where('status', 'hadir')->count(),
            'sakit' => $todayAttendances->where('status', 'sakit')->count(),
            'izin' => $todayAttendances->where('status', 'izin')->count(),
            'alpha' => $todayAttendances->where('status', 'alpha')->count(),
            'belum_absen' => $holiday ? 0 : $todayAttendances->where('status', 'belum_absen')->count(),
        ];
        
        // Data untuk tabel (limit 10 untuk dashboard)
        $recentAttendances = $students->map(function ($student) use ($todayAttendances) {
            $attendance = $todayAttendances->get($student->id);
            return [
                'student' => $student,
                'status' => $attendance ? $attendance->status : 'belum_absen',
                'attendance_time' => $attendance ? $attendance->attendance_time : null,
                'class' => $student->student ? $student->student->class : '-',
            ];
        })->take(10);
        
        return view('sekretaris.dashboard', compact(
            'today',
            'stats',
            'recentAttendances',
            'holiday'
        ));
    }

    // Absensi Harian (Simple Version)
    public function simpleAttendance(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        
        $holiday = Holiday::where('date', $selectedDate)->first();
        
        if ($holiday) {
            // Holiday date - no attendances needed
            $attendances = collect();
        } else {
            $attendances = Attendance::where('date', $selectedDate)->get()->keyBy('student_id');
            
            foreach ($students as $student) {
                if (!$attendances->has($student->id)) {
                    $attendance = Attendance::create([
                        'student_id' => $student->id,
                        'date' => $selectedDate,
                        'status' => 'belum_absen',
                        'attendance_time' => null,
                        'created_by' => auth()->id()
                    ]);
                    $attendances->put($student->id, $attendance);
                }
            }
        }
        
        return view('sekretaris.absensi', compact('students', 'attendances', 'selectedDate', 'holiday'));
    }

    public function batchUpdateAttendance(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $statuses = $request->input('status', []);
        $holidayNote = $request->input('holiday_note');
        
        // Save holiday if provided
        if ($holidayNote !== null && trim($holidayNote) !== '') {
            Holiday::updateOrCreate(
                ['date' => $date],
                [
                    'note' => $holidayNote,
                    'created_by' => auth()->id()
                ]
            );
        }
        
        foreach ($statuses as $studentId => $status) {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date
                ],
                [
                    'status' => $status,
                    'attendance_time' => $status === 'hadir' ? now()->format('H:i:s') : null,
                    'created_by' => auth()->id()
                ]
            );
        }
        
        return redirect()->route('sekretaris.absensi', ['date' => $date])
            ->with('success', 'Absensi dan keterangan libur berhasil disimpan!');
    }

    public function deleteHoliday(Request $request)
    {
        $date = $request->input('date');
        Holiday::where('date', $date)->delete();
        return redirect()->route('sekretaris.absensi', ['date' => $date])
            ->with('success', 'Keterangan libur dihapus!');
    }

    
    
// Attendance Tracker (Simple Version)
    public function simpleTracker()
    {
        $currentMonth = request('month', now()->month);
        $currentYear = now()->year;
        
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        
        // Get all attendance data for current month (not grouped yet)
        $allAttendances = Attendance::whereMonth('date', $currentMonth)
                                ->whereYear('date', $currentYear)
                                ->orderBy('date')
                                ->get();
        
        // Get holidays for current month to exclude from working days
        $holidays = Holiday::whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear)
                        ->pluck('date')
                        ->map(function($date) {
                            return $date->format('Y-m-d');
                        })
                        ->toArray();
        
        // Calculate total working days in month (excluding weekends and holidays)
        $daysInMonth = \Carbon\Carbon::create($currentYear, $currentMonth)->daysInMonth;
        $workingDays = 0;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
            $dateString = $date->format('Y-m-d');
            // Skip if Saturday (6) or Sunday (0) or holiday
            $dayOfWeek = $date->dayOfWeek;
            if (!in_array($dayOfWeek, [0, 6]) && !in_array($dateString, $holidays)) {
                $workingDays++;
            }
        }
        
        // Group attendances by student_id for easy lookup
        $attendances = $allAttendances->groupBy('student_id');
        
        // Calculate total per status (each student = 1 count, not each attendance)
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        
        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());
            
            // Check each attendance and exclude holidays from 'belum_absen' count
            foreach ($studentAttendances as $attendance) {
                $dateString = $attendance->date->format('Y-m-d');
                $isHoliday = in_array($dateString, $holidays);
                
                // Skip counting 'belum_absen' if it's a holiday
                if ($attendance->status === 'belum_absen' && $isHoliday) {
                    continue;
                }
                
                // Count each status (but don't double count per student)
                if ($attendance->status === 'hadir') {
                    $totalHadir++;
                } elseif ($attendance->status === 'sakit') {
                    $totalSakit++;
                } elseif ($attendance->status === 'izin') {
                    $totalIzin++;
                } elseif ($attendance->status === 'alpha') {
                    $totalAlpha++;
                }
            }
        }
        
        // Total attendances in the month (count each student once per status)
        $totalAttendances = $totalHadir + $totalSakit + $totalIzin + $totalAlpha;
        
        // Calculate working days used (based on how many students have any attendance record)
        // If workingDays = 10 and 3 students, total possible = 30
        // But simpler: just use workingDays as reference
        $totalDays = $workingDays;
        
        return view('sekretaris.tracker', compact(
            'students',
            'attendances',
            'currentMonth',
            'currentYear',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpha',
            'totalDays',
            'workingDays'
        ));
    }

    // API untuk detail attendance siswa
    public function getStudentAttendance($studentId)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        
        $attendances = Attendance::where('student_id', $studentId)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->orderBy('date')
                                ->get();
        
        // Fix: Format date ke string agar bisa jadi key array
        $holidays = Holiday::whereMonth('date', $month)
                          ->whereYear('date', $year)
                          ->get()
                          ->mapWithKeys(function ($holiday) {
                              return [$holiday->date->format('Y-m-d') => $holiday->note];
                          });
        
        // Transform data untuk JSON response yang aman
        $data = $attendances->map(function ($attendance) use ($holidays) {
            $dateString = $attendance->date ? $attendance->date->format('Y-m-d') : null;
            $holidayNote = $holidays[$dateString] ?? null;
            
            // Jika hari libur dan status belum_absen, ubah menjadi 'libur'
            $status = $attendance->status;
            if ($holidayNote && $status === 'belum_absen') {
                $status = 'libur';
            }
            
            return [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'date' => $dateString,
                'status' => $status,
                'attendance_time' => $attendance->attendance_time ? $attendance->attendance_time->format('H:i:s') : null,
                'created_by' => $attendance->created_by,
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at,
                'holiday_note' => $holidayNote,
            ];
        });
        
        return response()->json($data);
    }
}

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

    /**
     * Halaman utama laporan absensi
     */
    public function laporan()
    {
        // Prepare months and years for dropdown
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Generate years list (current year and 2 years back)
        $years = [];
        for ($i = 0; $i < 3; $i++) {
            $years[] = $currentYear - $i;
        }
        
        return view('sekretaris.laporan', compact('months', 'currentMonth', 'currentYear', 'years'));
    }

    /**
     * Cetak laporan absensi (HTML version)
     */
    public function cetakAbsensi($month, $year)
    {
        // Validate month and year
        if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
            abort(404, 'Invalid month or year');
        }
        
        // Get attendance data
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->with('student')
                        ->orderBy('name')
                        ->get();
        
        $attendances = Attendance::whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->with('student')
                                ->orderBy('date')
                                ->get();
        
        // Get holidays (exclude weekends from holiday info display)
        $allHolidays = Holiday::whereMonth('date', $month)
                             ->whereYear('date', $year)
                             ->get();
        
        // Filter holidays to exclude weekends for display
        $holidays = $allHolidays->filter(function ($holiday) {
            $dayOfWeek = $holiday->date->dayOfWeek;
            // 0 = Sunday, 6 = Saturday
            return !in_array($dayOfWeek, [0, 6]);
        })->mapWithKeys(function ($holiday) {
            return [$holiday->date->format('Y-m-d') => $holiday->note];
        });
        
        // Calculate statistics (use all holidays for calculation)
        $allHolidaysForCalc = $allHolidays->mapWithKeys(function ($holiday) {
            return [$holiday->date->format('Y-m-d') => $holiday->note];
        });
        $stats = $this->calculateAttendanceStats($students, $attendances, $allHolidaysForCalc, $month, $year);
        
        // Group attendances by student for easy display
        $attendancesByStudent = $attendances->groupBy('student_id');
        
        $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');
        
        return view('sekretaris.laporan-absensi-cetak', compact(
            'students', 
            'attendancesByStudent', 
            'holidays', 
            'stats', 
            'month', 
            'year', 
            'monthName'
        ));
    }

    /**
     * Generate PDF laporan absensi
     */
    public function laporanAbsensiPdf($month, $year)
    {
        // Validate month and year
        if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
            abort(404, 'Invalid month or year');
        }
        
        // Get attendance data (same as cetakAbsensi)
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->with('student')
                        ->orderBy('name')
                        ->get();
        
        $attendances = Attendance::whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->with('student')
                                ->orderBy('date')
                                ->get();
        
        // Get holidays (exclude weekends from holiday info display)
        $allHolidays = Holiday::whereMonth('date', $month)
                             ->whereYear('date', $year)
                             ->get();
        
        // Filter holidays to exclude weekends for display
        $holidays = $allHolidays->filter(function ($holiday) {
            $dayOfWeek = $holiday->date->dayOfWeek;
            // 0 = Sunday, 6 = Saturday
            return !in_array($dayOfWeek, [0, 6]);
        })->mapWithKeys(function ($holiday) {
            return [$holiday->date->format('Y-m-d') => $holiday->note];
        });
        
        // Calculate statistics (use all holidays for calculation)
        $allHolidaysForCalc = $allHolidays->mapWithKeys(function ($holiday) {
            return [$holiday->date->format('Y-m-d') => $holiday->note];
        });
        $stats = $this->calculateAttendanceStats($students, $attendances, $allHolidaysForCalc, $month, $year);
        
        // Group attendances by student for easy display
        $attendancesByStudent = $attendances->groupBy('student_id');
        
        $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');
        
        // Generate PDF
        $pdf = Pdf::loadView('sekretaris.laporan-absensi-cetak', compact(
            'students', 
            'attendancesByStudent', 
            'holidays', 
            'stats', 
            'month', 
            'year', 
            'monthName'
        ));
        
        $pdf->setPaper('a4', 'landscape'); // Landscape for better table display
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'isFontSubsettingEnabled' => true,
            'dpi' => 150
        ]);
        
        $filename = 'laporan-absensi-' . strtolower(str_replace(' ', '-', $monthName)) . '.pdf';
        
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Calculate attendance statistics
     */
    private function calculateAttendanceStats($students, $attendances, $holidays, $month, $year)
    {
        // Calculate working days (excluding weekends and holidays)
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $workingDays = 0;
        $workingDates = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateString = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;
            
            // Skip if Saturday (6) or Sunday (0) or holiday
            if (!in_array($dayOfWeek, [0, 6]) && !$holidays->has($dateString)) {
                $workingDays++;
                $workingDates[] = $dateString;
            }
        }
        
        // Initialize counters
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        $totalBelumAbsen = 0;
        
        // Count attendances
        foreach ($attendances as $attendance) {
            $dateString = $attendance->date->format('Y-m-d');
            $isHoliday = $holidays->has($dateString);
            
            // Skip counting 'belum_absen' if it's a holiday
            if ($attendance->status === 'belum_absen' && $isHoliday) {
                continue;
            }
            
            switch ($attendance->status) {
                case 'hadir':
                    $totalHadir++;
                    break;
                case 'sakit':
                    $totalSakit++;
                    break;
                case 'izin':
                    $totalIzin++;
                    break;
                case 'alpha':
                    $totalAlpha++;
                    break;
                case 'belum_absen':
                    $totalBelumAbsen++;
                    break;
            }
        }
        
        // Calculate percentage
        $totalPossibleAttendances = $students->count() * $workingDays;
        $attendanceRate = $totalPossibleAttendances > 0 ? round(($totalHadir / $totalPossibleAttendances) * 100, 2) : 0;
        
        return [
            'workingDays' => $workingDays,
            'totalStudents' => $students->count(),
            'totalHadir' => $totalHadir,
            'totalSakit' => $totalSakit,
            'totalIzin' => $totalIzin,
            'totalAlpha' => $totalAlpha,
            'totalBelumAbsen' => $totalBelumAbsen,
            'attendanceRate' => $attendanceRate,
            'totalPossibleAttendances' => $students->count() * $workingDays
        ];
    }
}

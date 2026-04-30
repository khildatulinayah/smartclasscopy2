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
        
        // Hitung statistik
        $stats = [
            'total' => $students->count(),
            'hadir' => $todayAttendances->where('status', 'hadir')->count(),
            'sakit' => $todayAttendances->where('status', 'sakit')->count(),
            'izin' => $todayAttendances->where('status', 'izin')->count(),
            'alpha' => $todayAttendances->where('status', 'alpha')->count(),
            'belum_absen' => $todayAttendances->where('status', 'belum_absen')->count(),
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
        
        // Cek hari libur
        $holiday = Holiday::where('date', $today)->first();
        
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

    public function quickUpdateAttendance(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:belum_absen,hadir,sakit,izin,alpha'
        ]);

        $today = now()->toDateString();
        
        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'date' => $today
            ],
            [
                'status' => $request->status,
                'attendance_time' => $request->status === 'hadir' ? now()->format('H:i:s') : null,
                'created_by' => auth()->id()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status absensi berhasil diperbarui',
            'attendance' => $attendance
        ]);
    }

    public function getTodayAttendance()
    {
        $today = now()->toDateString();
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        
        $attendances = Attendance::where('date', $today)->get()->keyBy('student_id');
        
        $data = [];
        foreach ($students as $student) {
            $attendance = $attendances->get($student->id);
            $data[] = [
                'id' => $student->id,
                'name' => $student->name,
                'status' => $attendance ? $attendance->status : 'belum_absen',
                'attendance_time' => $attendance ? $attendance->attendance_time : null
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
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
            // Count unique dates with each status for this student
            $statuses = $studentAttendances->pluck('status')->toArray();
            if (in_array('hadir', $statuses)) $totalHadir++;
            if (in_array('sakit', $statuses)) $totalSakit++;
            if (in_array('izin', $statuses)) $totalIzin++;
            if (in_array('alpha', $statuses)) $totalAlpha++;
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
            
            return [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'date' => $dateString,
                'status' => $attendance->status,
                'attendance_time' => $attendance->attendance_time ? $attendance->attendance_time->format('H:i:s') : null,
                'created_by' => $attendance->created_by,
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at,
                'holiday_note' => $holidays[$dateString] ?? null,
            ];
        });
        
        return response()->json($data);
    }

    // Daftar Siswa
    public function studentList()
    {
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        return view('sekretaris.student-list', compact('students'));
    }

    // Laporan Absensi
    public function laporanAbsensi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // kalau belum pilih bulan → tampilkan halaman filter dulu
        if (!$bulan || !$tahun) {
            return view('sekretaris.laporan-filter', compact('bulan', 'tahun'));
        }

        // kalau sudah pilih → baru generate laporan
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->get()
            ->groupBy('student_id');

        $jumlahHari = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;

        $laporan = [];

        foreach ($students as $student) {
            $dataPerHari = [];
            $dataAbsensi = $attendances[$student->id] ?? collect();

            for ($i = 1; $i <= $jumlahHari; $i++) {
                $tanggal = \Carbon\Carbon::create($tahun, $bulan, $i)->toDateString();
                $absen = $dataAbsensi->firstWhere('date', $tanggal);
                $dataPerHari[$i] = $absen ? $absen->status : '-';
            }

            $total = [
                'hadir' => $dataAbsensi->where('status', 'hadir')->count(),
                'sakit' => $dataAbsensi->where('status', 'sakit')->count(),
                'izin'  => $dataAbsensi->where('status', 'izin')->count(),
                'alpha' => $dataAbsensi->where('status', 'alpha')->count(),
            ];

            $laporan[] = [
                'nama' => $student->name,
                'hari' => $dataPerHari,
                'total' => $total
            ];
        }

        return view('sekretaris.laporan', compact(
            'laporan',
            'bulan',
            'tahun',
            'jumlahHari'
        ));
    }

    public function cetakAbsensi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->get()
            ->groupBy('student_id');

        $jumlahHari = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;

        $laporan = [];

        foreach ($students as $student) {
            $dataPerHari = [];
            $dataAbsensi = $attendances[$student->id] ?? collect();

            for ($i = 1; $i <= $jumlahHari; $i++) {
                $tanggal = \Carbon\Carbon::create($tahun, $bulan, $i)->toDateString();
                $absen = $dataAbsensi->firstWhere('date', $tanggal);
                $dataPerHari[$i] = $absen ? $absen->status : '-';
            }

            $total = [
                'hadir' => $dataAbsensi->where('status', 'hadir')->count(),
                'sakit' => $dataAbsensi->where('status', 'sakit')->count(),
                'izin'  => $dataAbsensi->where('status', 'izin')->count(),
                'alpha' => $dataAbsensi->where('status', 'alpha')->count(),
            ];

            $laporan[] = [
                'nama' => $student->name,
                'hari' => $dataPerHari,
                'total' => $total
            ];
        }

        $pdf = Pdf::loadView('sekretaris.laporan-cetak', compact(
            'laporan',
            'bulan',
            'tahun',
            'jumlahHari'
        ))->setPaper('a3', 'landscape');

        return $pdf->stream("laporan-absensi-$bulan-$tahun.pdf");
    }
}

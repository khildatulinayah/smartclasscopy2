<?php

namespace App\Http\Controllers;

/**
 * Sekretaris Controller - Mengelola absensi dan laporan
 * MVC Pattern: Model (data) -> Controller (logic) -> View (tampilan)
 */

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Holiday;

class SekretarisController extends Controller
{
    // ============= HELPER METHODS =============
    
    /**
     * Check if date is weekend or holiday
     */
    private function isWeekendOrHoliday($date, $holidays = null)
    {
        $dateString = is_string($date) ? $date : $date->format('Y-m-d');
        $carbonDate = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        
        // Check weekend
        if ($carbonDate->isWeekend()) {
            return true;
        }
        
        // Check holiday if provided
        if ($holidays && $holidays->has($dateString)) {
            return true;
        }
        
        return false;
    }

    // ============= DASHBOARD =============
    /**
     * Dashboard - Halaman utama sekretaris
     */
    public function dashboard()
    {
        $today = now()->toDateString();
        
        // Ambil semua siswa aktif
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->with('student')
                        ->orderBy('name', 'asc')
                        ->get();
        
        // Data absensi hari ini
        $todayAttendances = Attendance::where('date', $today)
                                      ->with('student')
                                      ->get()
                                      ->keyBy('student_id');
        
        // Cek hari libur
        $holiday = Holiday::where('date', $today)->first();
        
        // Buat record absensi jika belum ada
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
        
        // Hitung statistik absensi
        $stats = [
            'total' => $students->count(),
            'hadir' => $todayAttendances->where('status', 'hadir')->count(),
            'sakit' => $todayAttendances->where('status', 'sakit')->count(),
            'izin' => $todayAttendances->where('status', 'izin')->count(),
            'alpha' => $todayAttendances->where('status', 'alpha')->count(),
            'belum_absen' => $holiday ? 0 : $todayAttendances->where('status', 'belum_absen')->count(),
        ];
        
        // Data untuk dashboard (limit 10)
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

    // ============= ABSENSI =============
    
    /**
     * Simple Attendance - Absensi harian
     */
    public function simpleAttendance(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name', 'asc')->get();
        
        // Cek holiday dari database
        $holiday = Holiday::where('date', $selectedDate)->first();
        
        if ($this->isWeekendOrHoliday($selectedDate)) {
            // Hari libur/weekend - tidak perlu absensi manual
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

    /**
     * Batch Update Attendance - Update absensi batch
     */
    public function batchUpdateAttendance(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $statuses = $request->input('status', []);
        $holidayNote = $request->input('holiday_note');
        
        // Simpan keterangan libur
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

    /**
     * Delete Holiday - Hapus keterangan libur
     */
    public function deleteHoliday(Request $request)
    {
        $date = $request->input('date');
        Holiday::where('date', $date)->delete();
        return redirect()->route('sekretaris.absensi', ['date' => $date])
            ->with('success', 'Keterangan libur dihapus!');
    }

    /**
     * Mark All Present - Tandai semua siswa sebagai hadir
     */
    public function markAllPresent(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        
        // Cek apakah hari ini adalah weekend atau libur
        if ($this->isWeekendOrHoliday($date)) {
            $holiday = Holiday::where('date', $date)->first();
            $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
            $weekendName = $dayOfWeek == 0 ? 'Minggu' : 'Sabtu';
            
            $reason = $holiday ? 'hari libur: ' . $holiday->note : "hari {$weekendName} (hari libur)";
            
            return redirect()->route('sekretaris.absensi', ['date' => $date])
                ->with('error', "Tidak dapat menandai hadir semua karena hari ini adalah {$reason}!");
        }
        
        // Ambil semua siswa aktif
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->get();
        
        // Update semua siswa menjadi hadir
        foreach ($students as $student) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date
                ],
                [
                    'status' => 'hadir',
                    'attendance_time' => now()->format('H:i:s'),
                    'created_by' => auth()->id()
                ]
            );
        }
        
        return redirect()->route('sekretaris.absensi', ['date' => $date])
            ->with('success', 'Semua siswa berhasil ditandai sebagai hadir!');
    }

    // ============= TRACKER =============
    
    /**
     * Simple Tracker - Tracker absensi bulanan
     */
    public function simpleTracker()
    {
        $currentMonth = request('month', now()->month);
        $currentYear = now()->year;
        
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name', 'asc')->get();
        
        // Data absensi bulan ini
        $allAttendances = Attendance::whereMonth('date', $currentMonth)
                                ->whereYear('date', $currentYear)
                                ->orderBy('date')
                                ->get();
        
        // Data hari libur bulan ini
        $holidays = Holiday::whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear)
                        ->pluck('date')
                        ->map(function($date) {
                            return $date->format('Y-m-d');
                        })
                        ->toArray();
        
        // Hitung hari kerja bulan ini
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
        
        // Kelompokkan absensi per siswa
        $attendances = $allAttendances->groupBy('student_id');
        
        // Hitung total per status
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        
        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());
            
            // Cek absensi dan exclude libur
            foreach ($studentAttendances as $attendance) {
                $dateString = $attendance->date->format('Y-m-d');
                $isHoliday = in_array($dateString, $holidays);
                $date = \Carbon\Carbon::create($currentYear, $currentMonth, $attendance->date->day);
                $dayOfWeek = $date->dayOfWeek;
                
                // Auto mark weekend sebagai 'libur'
                if (($dayOfWeek == 0 || $dayOfWeek == 6) && $attendance->status === 'belum_absen') {
                    $attendance->status = 'libur';
                    $attendance->holiday_note = 'Hari Libur Sabtu/Minggu';
                }
                // Auto mark libur sebagai 'libur'
                elseif ($isHoliday && $attendance->status === 'belum_absen') {
                    $attendance->status = 'libur';
                    $attendance->holiday_note = 'Hari Libur';
                }
                
                // Skip hitung 'belum_absen' jika libur/weekend
                if ($attendance->status === 'belum_absen' && ($isHoliday || $dayOfWeek == 0 || $dayOfWeek == 6)) {
                    continue;
                }
                
                // Hitung per status (jangan double count)
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
        
        // Total absensi bulan ini
        $totalAttendances = $totalHadir + $totalSakit + $totalIzin + $totalAlpha;
        
        // Hitung hari kerja yang digunakan
        // Gunakan workingDays sebagai referensi
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

    // ============= API =============
    
    /**
     * Get Student Attendance - API detail absensi siswa
     */
    public function getStudentAttendance($studentId)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        
        $attendances = Attendance::where('student_id', $studentId)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->orderBy('date')
                                ->get();
        
        // Format date untuk key array
        $holidays = Holiday::whereMonth('date', $month)
                          ->whereYear('date', $year)
                          ->get()
                          ->mapWithKeys(function ($holiday) {
                              return [$holiday->date->format('Y-m-d') => $holiday->note];
                          });
        
        // Transform data untuk JSON response
        $data = $attendances->map(function ($attendance) use ($holidays, $month, $year) {
            $dateString = $attendance->date ? $attendance->date->format('Y-m-d') : null;
            $holidayNote = $holidays[$dateString] ?? null;
            
            // Cek apakah weekend
            $date = \Carbon\Carbon::create($year, $month, $attendance->date->day);
            $dayOfWeek = $date->dayOfWeek;
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            
            // Ubah 'belum_absen' jadi 'libur' jika hari libur
            $status = $attendance->status;
            $holidayNoteToUse = $holidayNote;
            
            if ($isWeekend && $status === 'belum_absen') {
                $status = 'libur';
                $holidayNoteToUse = 'Hari Libur Sabtu/Minggu';
            } elseif ($holidayNote && $status === 'belum_absen') {
                $status = 'libur';
                $holidayNoteToUse = 'Hari Libur';
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
                'holiday_note' => $holidayNoteToUse,
            ];
        });
        
        return response()->json($data);
    }

    // ============= LAPORAN =============
    
    /**
     * Laporan - Halaman utama laporan
     */
    public function laporan()
    {
        // Data bulan dan tahun untuk dropdown
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Generate list tahun (2 tahun ke belakang)
        $years = [];
        for ($i = 0; $i < 3; $i++) {
            $years[] = $currentYear - $i;
        }
        
        return view('sekretaris.laporan', compact('months', 'currentMonth', 'currentYear', 'years'));
    }

    /**
     * Prepare Laporan Data - Common data untuk cetak dan PDF
     */
    private function prepareLaporanData($month, $year)
    {
        // Validate month and year
        if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
            abort(404, 'Invalid month or year');
        }
        
        // Get attendance data
        $students = User::where('role', 'siswa')
                        ->where('is_active', true)
                        ->with('student')
                        ->orderBy('name', 'asc')
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
            return !$holiday->date->isWeekend();
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
        
        return compact(
            'students', 
            'attendancesByStudent', 
            'holidays', 
            'stats', 
            'month', 
            'year', 
            'monthName'
        );
    }

    /**
     * Cetak Absensi - Cetak laporan absensi
     */
    public function cetakAbsensi($month, $year)
    {
        $data = $this->prepareLaporanData($month, $year);
        
        return view('sekretaris.laporan-absensi-cetak', $data);
    }

    /**
     * Laporan Absensi PDF - Export PDF laporan
     */
    public function laporanAbsensiPdf($month, $year)
    {
        $data = $this->prepareLaporanData($month, $year);
        
        // Generate PDF
        $pdf = Pdf::loadView('sekretaris.laporan-absensi-cetak', $data);
        
        $pdf->setPaper('a4', 'landscape'); // Landscape untuk tabel
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'isFontSubsettingEnabled' => true,
            'dpi' => 150
        ]);
        
        $filename = 'laporan-absensi-' . strtolower(str_replace(' ', '-', $data['monthName'])) . '.pdf';
        
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Calculate Attendance Stats - Hitung statistik absensi
     */
    private function calculateAttendanceStats($students, $attendances, $holidays, $month, $year)
    {
        // Hitung hari kerja (exclude weekend/libur)
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $workingDays = 0;
        $workingDates = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateString = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;
            
            // Skip if weekend or holiday
            if (!$this->isWeekendOrHoliday($date, $holidays)) {
                $workingDays++;
                $workingDates[] = $dateString;
            }
        }
        
        // Inisialisasi counter
        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        $totalBelumAbsen = 0;
        
        // Hitung data absensi
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
        
        // Hitung persentase kehadiran
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

    // ============= HOLIDAY API =============

    /**
     * Get Holidays - Daftar hari libur dengan filter
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHolidays(Request $request)
    {
        try {
            $query = Holiday::query();

            // Filter berdasarkan bulan dan tahun
            if ($request->has('month') && $request->has('year')) {
                $month = (int)$request->input('month');
                $year = (int)$request->input('year');
                $query->whereMonth('date', $month)->whereYear('date', $year);
            }

            // Filter berdasarkan range tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            }

            // Filter berdasarkan pencarian note
            if ($request->has('search')) {
                $query->where('note', 'like', '%' . $request->input('search') . '%');
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'date');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $holidays = $query->with('creator')
                            ->paginate($request->input('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $holidays->items(),
                'pagination' => [
                    'total' => $holidays->total(),
                    'per_page' => $holidays->perPage(),
                    'current_page' => $holidays->currentPage(),
                    'last_page' => $holidays->lastPage(),
                    'from' => $holidays->firstItem(),
                    'to' => $holidays->lastItem(),
                ],
                'message' => 'Data hari libur berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Indonesian national public holidays for a given year.
     * @param Request $request
     * @param int|null $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndonesianNationalHolidays(Request $request, $year = null)
    {
        try {
            $year = $year ?? $request->input('year', now()->year);
            $year = (int) $year;

            if ($year < 2020 || $year > 2100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun tidak valid. Pilih tahun antara 2020 dan 2100.'
                ], 400);
            }

            $holidays = Holiday::getIndonesianNationalHolidays($year)
                ->map(function ($holiday) {
                    return [
                        'date' => $holiday['date'],
                        'day_name' => Carbon::parse($holiday['date'])->locale('id')->translatedFormat('l'),
                        'note' => $holiday['note'],
                    ];
                });

            return response()->json([
                'success' => true,
                'year' => $year,
                'data' => $holidays,
                'message' => 'Data hari libur nasional Indonesia berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hari libur nasional: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Holidays by Month and Year - Daftar hari libur berdasarkan bulan/tahun
     * @param int $month
     * @param int $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHolidaysByMonth($month, $year)
    {
        try {
            if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulan atau tahun tidak valid'
                ], 400);
            }

            $holidays = Holiday::whereMonth('date', $month)
                              ->whereYear('date', $year)
                              ->with('creator')
                              ->orderBy('date', 'asc')
                              ->get()
                              ->map(function ($holiday) {
                                  return [
                                      'id' => $holiday->id,
                                      'date' => $holiday->date->format('Y-m-d'),
                                      'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                                      'note' => $holiday->note,
                                      'created_by' => $holiday->created_by,
                                      'creator_name' => $holiday->creator ? $holiday->creator->name : '-',
                                      'created_at' => $holiday->created_at->format('Y-m-d H:i:s'),
                                  ];
                              });

            return response()->json([
                'success' => true,
                'data' => $holidays,
                'count' => $holidays->count(),
                'month' => $month,
                'year' => $year,
                'message' => 'Data hari libur berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store Holiday - Tambah hari libur baru
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeHoliday(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'date' => 'required|date|date_format:Y-m-d',
                'note' => 'required|string|min:3|max:255'
            ], [
                'date.required' => 'Tanggal harus diisi',
                'date.date' => 'Format tanggal harus Y-m-d',
                'date.date_format' => 'Format tanggal harus Y-m-d',
                'note.required' => 'Keterangan hari libur harus diisi',
                'note.min' => 'Keterangan minimal 3 karakter',
                'note.max' => 'Keterangan maksimal 255 karakter',
            ]);

            // Cek apakah tanggal sudah ada
            $existingHoliday = Holiday::where('date', $validated['date'])->first();
            if ($existingHoliday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal ' . $validated['date'] . ' sudah terdaftar sebagai hari libur: ' . $existingHoliday->note
                ], 400);
            }

            // Buat holiday baru
            $holiday = Holiday::create([
                'date' => $validated['date'],
                'note' => $validated['note'],
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                    'note' => $holiday->note,
                    'created_by' => $holiday->created_by,
                    'creator_name' => auth()->user()->name,
                    'created_at' => $holiday->created_at->format('Y-m-d H:i:s'),
                ],
                'message' => 'Hari libur berhasil ditambahkan'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Holiday - Ubah hari libur
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHoliday(Request $request, $id)
    {
        try {
            // Cari holiday
            $holiday = Holiday::find($id);
            if (!$holiday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari libur tidak ditemukan'
                ], 404);
            }

            // Validasi input
            $validated = $request->validate([
                'date' => 'required|date|date_format:Y-m-d',
                'note' => 'required|string|min:3|max:255'
            ], [
                'date.required' => 'Tanggal harus diisi',
                'date.date' => 'Format tanggal harus Y-m-d',
                'date.date_format' => 'Format tanggal harus Y-m-d',
                'note.required' => 'Keterangan hari libur harus diisi',
                'note.min' => 'Keterangan minimal 3 karakter',
                'note.max' => 'Keterangan maksimal 255 karakter',
            ]);

            // Cek apakah tanggal sudah ada (kecuali untuk holiday yang sedang di-update)
            $duplicateHoliday = Holiday::where('date', $validated['date'])
                                       ->where('id', '!=', $id)
                                       ->first();
            if ($duplicateHoliday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal ' . $validated['date'] . ' sudah terdaftar sebagai hari libur: ' . $duplicateHoliday->note
                ], 400);
            }

            // Update holiday
            $holiday->update([
                'date' => $validated['date'],
                'note' => $validated['note']
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                    'note' => $holiday->note,
                    'created_by' => $holiday->created_by,
                    'updated_at' => $holiday->updated_at->format('Y-m-d H:i:s'),
                ],
                'message' => 'Hari libur berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Holiday - Ambil detail hari libur
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHoliday($id)
    {
        try {
            $holiday = Holiday::with('creator')->find($id);
            
            if (!$holiday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari libur tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                    'note' => $holiday->note,
                    'created_by' => $holiday->created_by,
                    'creator_name' => $holiday->creator ? $holiday->creator->name : '-',
                    'created_at' => $holiday->created_at->format('Y-m-d H:i:s'),
                ],
                'message' => 'Data hari libur berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Holiday - Hapus hari libur
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteHolidayApi($id)
    {
        try {
            $holiday = Holiday::find($id);
            
            if (!$holiday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari libur tidak ditemukan'
                ], 404);
            }

            $holidayData = [
                'id' => $holiday->id,
                'date' => $holiday->date->format('Y-m-d'),
                'note' => $holiday->note
            ];

            $holiday->delete();

            return response()->json([
                'success' => true,
                'data' => $holidayData,
                'message' => 'Hari libur berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Add Holidays - Tambah banyak hari libur sekaligus
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAddHolidays(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'holidays' => 'required|array|min:1',
                'holidays.*.date' => 'required|date|date_format:Y-m-d',
                'holidays.*.note' => 'required|string|min:3|max:255'
            ], [
                'holidays.required' => 'Data hari libur harus diisi',
                'holidays.array' => 'Data hari libur harus berupa array',
                'holidays.min' => 'Minimal 1 hari libur harus ditambahkan',
                'holidays.*.date.required' => 'Tanggal harus diisi',
                'holidays.*.date.date' => 'Format tanggal harus Y-m-d',
                'holidays.*.date.date_format' => 'Format tanggal harus Y-m-d',
                'holidays.*.note.required' => 'Keterangan hari libur harus diisi',
                'holidays.*.note.min' => 'Keterangan minimal 3 karakter',
                'holidays.*.note.max' => 'Keterangan maksimal 255 karakter',
            ]);

            $holidays = [];
            $errors = [];
            $userId = auth()->id();

            foreach ($validated['holidays'] as $index => $holidayData) {
                // Cek apakah tanggal sudah ada
                $existingHoliday = Holiday::where('date', $holidayData['date'])->first();
                
                if ($existingHoliday) {
                    $errors[] = [
                        'index' => $index,
                        'date' => $holidayData['date'],
                        'message' => 'Tanggal sudah terdaftar: ' . $existingHoliday->note
                    ];
                    continue;
                }

                // Buat holiday
                $holiday = Holiday::create([
                    'date' => $holidayData['date'],
                    'note' => $holidayData['note'],
                    'created_by' => $userId
                ]);

                $holidays[] = [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                    'note' => $holiday->note,
                    'created_at' => $holiday->created_at->format('Y-m-d H:i:s'),
                ];
            }

            $status = empty($errors) ? true : false;
            $message = count($holidays) . ' hari libur berhasil ditambahkan';
            if (!empty($errors)) {
                $message .= ', ' . count($errors) . ' hari libur gagal ditambahkan';
            }

            return response()->json([
                'success' => $status,
                'data' => $holidays,
                'errors' => $errors,
                'message' => $message,
                'created_count' => count($holidays),
                'failed_count' => count($errors)
            ], $status ? 201 : 207);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan hari libur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Holidays Summary - Ringkasan hari libur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHolidaysSummary(Request $request)
    {
        try {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);

            if ($month < 1 || $month > 12 || $year < 2020 || $year > 2030) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulan atau tahun tidak valid'
                ], 400);
            }

            // Total hari libur
            $totalHolidays = Holiday::whereMonth('date', $month)
                                    ->whereYear('date', $year)
                                    ->count();

            // Hari libur nasional vs hari libur lainnya (bisa ditambah logic untuk membedakan)
            $holidays = Holiday::whereMonth('date', $month)
                              ->whereYear('date', $year)
                              ->with('creator')
                              ->orderBy('date', 'asc')
                              ->get();

            // Hitung hari kerja (semua hari minus weekend minus holiday)
            $daysInMonth = Carbon::create($year, $month)->daysInMonth;
            $workingDays = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                if (!$date->isWeekend() && !$holidays->pluck('date')->map(function($d) { 
                    return $d->format('Y-m-d'); 
                })->contains($date->format('Y-m-d'))) {
                    $workingDays++;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'month' => $month,
                    'year' => $year,
                    'total_holidays' => $totalHolidays,
                    'total_weekend_days' => $daysInMonth - $workingDays - $totalHolidays,
                    'working_days' => $workingDays,
                    'days_in_month' => $daysInMonth,
                    'holidays_list' => $holidays->map(function ($holiday) {
                        return [
                            'id' => $holiday->id,
                            'date' => $holiday->date->format('Y-m-d'),
                            'day_name' => $holiday->date->locale('id')->translatedFormat('l'),
                            'note' => $holiday->note,
                        ];
                    })
                ],
                'message' => 'Ringkasan hari libur berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan hari libur: ' . $e->getMessage()
            ], 500);
        }
    }
}

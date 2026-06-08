<?php

namespace App\Http\Controllers;

/**
 * Siswa Controller - Portal siswa untuk melihat data pribadi
 * MVC Pattern: Model (data) -> Controller (logic) -> View (tampilan)
 */

use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Holiday;
use App\Models\KasSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SiswaController extends Controller
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
        
        // Check holiday in database if no holidays collection provided
        if (!$holidays && \App\Models\Holiday::where('date', $dateString)->exists()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get status text for display
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpha' => 'Alpha',
            'libur' => 'Libur',
            'belum_absen' => 'Belum Absen'
        ];
        
        return $statusTexts[$status] ?? 'Hadir';
    }

    // ============= DASHBOARD =============
    /**
     * Dashboard - Halaman utama siswa
     */
    public function dashboard()
    {
        $student = auth()->user();
        
        // Data absensi bulan ini
        $attendances = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();
        
        // Calculate attendance statistics
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $totalDays = $attendances->count();
        
        // Status absensi hari ini
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();
        
        // Cek apakah hari ini weekend atau libur
        if ($this->isWeekendOrHoliday($today)) {
            $statusHariIni = 'libur';
        } else {
            $statusHariIni = $todayAttendance ? $todayAttendance->status : 'belum_absen';
        }
        
        // Data transaksi bulan ini
        $transactions = Transaction::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();
            
        $totalPemasukan = $transactions->where('type', 'income')->sum('amount');
        $totalPengeluaran = $transactions->where('type', 'expense')->sum('amount');
        
        // Data pembayaran mingguan bulan ini
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $weeklyPayments = WeeklyPayment::where('student_id', $student->id)
                                ->where('month', $currentMonth)
                                ->where('year', $currentYear)
                                ->orderBy('week_number')
                                ->get();
        
        // Calculate payment statistics
        $totalWeeks = WeeklyPayment::getWeeksInMonth($currentMonth, $currentYear);
        $paidWeeks = $weeklyPayments->where('status', 'paid')->count();
        $unpaidWeeks = $weeklyPayments->where('status', 'unpaid')->count();
        $totalKasBulanan = $weeklyPayments->sum('amount');
        $kasSudahBayar = $weeklyPayments->where('status', 'paid')->sum('amount');
        $kasTunggakan = $weeklyPayments->where('status', 'unpaid')->sum('amount');
        
        // Status pembayaran
        $statusKas = 'Lunas';
        if ($unpaidWeeks > 0) {
            $statusKas = 'Ada Tunggakan';
        }
        if ($paidWeeks === 0) {
            $statusKas = 'Belum Bayar';
        }

        return view('siswa.dashboard', compact(
            //absensi data
            'attendances', 
            'transactions',
            'totalHadir', 
            'totalSakit', 
            'totalIzin', 
            'totalAlpha', 
            'totalDays',
            'statusHariIni',
            'totalPemasukan',
            'totalPengeluaran',
            // Weekly payment data
            'weeklyPayments',
            'totalWeeks',
            'paidWeeks',
            'unpaidWeeks',
            'totalKasBulanan',
            'kasSudahBayar',
            'kasTunggakan',
            'statusKas',
            'currentMonth',
            'currentYear'
        ));
    }

    // ============= ABSENSI =============
    
    /**
     * Absensi - Halaman riwayat absensi
     */
    public function absensi($month = null, $year = null)
    {
        $student = auth()->user();
        
        // Ambil bulan/tahun yang dipilih
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        // Create Carbon date for navigation
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        // Data hari libur bulan ini
        $holidays = Holiday::whereMonth('date', $month)
                          ->whereYear('date', $year)
                          ->get()
                          ->mapWithKeys(function ($holiday) {
                              return [$holiday->date->format('Y-m-d') => $holiday->note];
                          });
        
        // Data absensi untuk bulan yang dipilih
        // Pastikan urutan tanggal dari awal bulan sampai akhir bulan
        $attendances = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();

        // Pastikan urutan dan tipe date konsisten untuk tampilan tabel
        $attendances = $attendances->sortBy(function ($attendance) {
            return $attendance->date ? Carbon::parse($attendance->date)->format('Y-m-d') : '';
        })->values();
        
        // Transform data absensi untuk handle weekend/hari libur
        $attendances = $attendances->map(function ($attendance) use ($holidays) {
            // Kuatkan tipe date agar Carbon parsing konsisten
            $attendance->date = $attendance->date ? Carbon::parse($attendance->date) : null;
            $dateString = $attendance->date ? $attendance->date->format('Y-m-d') : null;
            $holidayNote = $holidays[$dateString] ?? null;
            
            // Ubah 'belum_absen' jadi 'libur' jika weekend atau hari libur
            $status = $attendance->status;
            if ($attendance->date && $this->isWeekendOrHoliday($attendance->date, $holidays) && $status === 'belum_absen') {
                $status = 'libur';
                if (!$holidayNote && $attendance->date->isWeekend()) {
                    $holidayNote = 'Hari Libur Akhir Pekan';
                }
            }
            
            $attendance->status = $status;
            $attendance->holiday_note = $holidayNote;
            
            return $attendance;
        });
        
        // Hitung statistik absensi (exclude hari libur)
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        
        return view('siswa.absensi', compact(
            'attendances',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpha',
            'currentDate',
            'prevMonth',
            'nextMonth'
        ));
    }

    // ============= PEMBAYARAN =============
    
    /**
     * Pembayaran - Halaman riwayat pembayaran
     */
    public function pembayaran($month = null, $year = null)
    {
        $student = auth()->user();
        
        // Ambil bulan/tahun yang dipilih
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        // Create Carbon date for navigation
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        // Data pembayaran mingguan untuk bulan yang dipilih
        $weeklyPayments = WeeklyPayment::where('student_id', $student->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->orderBy('week_number')
                                ->get();
        
        // Calculate payment statistics
        $totalWeeks = WeeklyPayment::getWeeksInMonth($month, $year);
        $paidWeeks = $weeklyPayments->where('status', 'paid')->count();
        $kasSudahBayar = $weeklyPayments->where('status', 'paid')->sum('amount');
        $currentKasNominal = KasSetting::getNominal((int) $month, (int) $year) ?? 0;
        
        // Data riwayat pembayaran untuk bulan yang dipilih
        $paymentHistory = Transaction::where('student_id', $student->id)
            ->where('type', 'income')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();
        
        return view('siswa.pembayaran', compact(
            'weeklyPayments',
            'totalWeeks',
            'paidWeeks',
            'kasSudahBayar',
            'currentKasNominal',
            'paymentHistory',
            'currentDate',
            'prevMonth',
            'nextMonth'
        ));
    }

    // ============= API =============
    
    /**
     * Get My Status - API status siswa
     */
    public function getMyStatus()
    {
        $student = auth()->user();
        
        // Data absensi terbaru
        $attendances = Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->map(function ($attendance) {
                return [
                    'date' => Carbon::parse($attendance->date)->format('d M Y'),
                    'status' => $attendance->status,
                    'status_text' => $this->getStatusText($attendance->status)
                ];
            });

        // Data transaksi terbaru
        $transactions = Transaction::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->take(6)
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => Carbon::parse($transaction->date)->format('d M Y'),
                    'type' => $transaction->type,
                    'type_text' => $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    'amount' => $transaction->amount,
                    'description' => $transaction->description
                ];
            });

        return response()->json([
            'attendances' => $attendances,
            'transactions' => $transactions,
            'total_paid' => Transaction::where('student_id', $student->id)->where('type', 'income')->sum('amount')
        ]);
    }

    // ============= PROFILE =============
    
    /**
     * Profile - Halaman profil siswa
     */
    public function profile()
    {
        $student = auth()->user();
        
        return view('siswa.profile', compact(
            'student'
        ));
    }
    
    /**
     * Update Profile - Update profil siswa
     */
    public function updateProfile(Request $request)
    {
        $student = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);
        
        $updateData = [
            'name' => $request->name,
            'gender' => $request->gender,
        ];
        
        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $student->id . '.' . $file->getClientOriginalExtension();
            
            // Store using Laravel storage system
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $updateData['profile_photo'] = $path;
        }
        
        $student->update($updateData);
        
        return redirect()->route('siswa.profile')->with('success', 'Profile berhasil diperbarui!');
    }
}

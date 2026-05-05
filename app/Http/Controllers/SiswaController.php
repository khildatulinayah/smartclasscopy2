<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Holiday;
use App\Models\KasSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user();
        
        // Get attendance history for current month
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
        
        // Get today's attendance status
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();
        
        // Cek apakah hari ini libur
        $isHoliday = Holiday::where('date', $today)->exists();
        
        if ($isHoliday) {
            $statusHariIni = 'libur';
        } else {
            $statusHariIni = $todayAttendance ? $todayAttendance->status : 'belum_absen';
        }
        
        // Get payment/transaction history for current month
        $transactions = Transaction::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();
            
        $totalPemasukan = $transactions->where('type', 'income')->sum('amount');
        $totalPengeluaran = $transactions->where('type', 'expense')->sum('amount');
        
        // Get weekly payment status for current month
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
        
        // Payment status text
        $statusKas = 'Lunas';
        if ($unpaidWeeks > 0) {
            $statusKas = 'Ada Tunggakan';
        }
        if ($paidWeeks === 0) {
            $statusKas = 'Belum Bayar';
        }

        return view('siswa.dashboard', compact(
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

    public function absensi($month = null, $year = null)
    {
        $student = auth()->user();
        
        // Get requested month/year or default to current
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        // Create Carbon date for navigation
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        // Get holidays for the month
        $holidays = Holiday::whereMonth('date', $month)
                          ->whereYear('date', $year)
                          ->get()
                          ->mapWithKeys(function ($holiday) {
                              return [$holiday->date->format('Y-m-d') => $holiday->note];
                          });
        
        // Get attendance history for specified month
        $attendances = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();
        
        // Transform attendance data to handle holidays
        $attendances = $attendances->map(function ($attendance) use ($holidays) {
            $dateString = $attendance->date->format('Y-m-d');
            $holidayNote = $holidays[$dateString] ?? null;
            
            // If it's a holiday and status is belum_absen, change to 'libur'
            $status = $attendance->status;
            if ($holidayNote && $status === 'belum_absen') {
                $status = 'libur';
            }
            
            $attendance->status = $status;
            $attendance->holiday_note = $holidayNote;
            
            return $attendance;
        });
        
        // Calculate attendance statistics (excluding holidays from 'belum_absen' count)
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

    public function pembayaran($month = null, $year = null)
    {
        $student = auth()->user();
        
        // Get requested month/year or default to current
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        // Create Carbon date for navigation
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        // Get weekly payment status for specified month
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
        
        // Get payment history for specified month
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

    public function getMyStatus()
    {
        $student = auth()->user();
        
        // Get recent attendance data
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

        // Get recent transaction data
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
}

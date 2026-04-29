<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Holiday;
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
        $totalWeeks = 4;
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

    // Method untuk riwayat absensi lengkap
    public function riwayatAbsensi(Request $request)
    {
        $student = auth()->user();
        
        // Query riwayat absensi siswa yang login
        $query = Attendance::where('student_id', $student->id);
        
        // Filter berdasarkan bulan jika ada
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        }
        
        // Ambil data dengan urutan terbaru
        $attendances = $query->orderBy('date', 'desc')->get();
        
        return view('siswa.riwayat-absensi', compact('attendances'));
    }
}

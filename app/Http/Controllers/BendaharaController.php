<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class BendaharaController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::now();
        $isWednesday = $today->dayOfWeek === 3;
        
        $currentMonth = $today->month;
        $currentYear = $today->year;
        
        // Get weeks in current month (dynamic based on Wednesdays)
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($currentMonth, $currentYear);
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($currentMonth, $currentYear);
        
        // Calculate current week based on actual Wednesdays
        $currentWeek = 1;
        if (!empty($wednesdayDates)) {
            $firstWednesday = $wednesdayDates[0];
            
            if ($today->gte($firstWednesday)) {
                foreach ($wednesdayDates as $index => $wednesday) {
                    if ($today->gte($wednesday)) {
                        $currentWeek = $index + 1;
                    }
                }
            }
        }
        
        $nextWednesday = $today->copy()->next(Carbon::WEDNESDAY)->format('d M Y');
        
        // Sync bills for current month (idempotent - create missing, don't duplicate)
        WeeklyPayment::syncMonthlyBills($currentMonth, $currentYear);
        
        $payments = WeeklyPayment::with(['student', 'transaction'])
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();
        
        // Get current week unpaid payments
        $currentWeekUnpaid = $payments
            ->where('week_number', $currentWeek)
            ->where('status', 'unpaid')
            ->count();
        
        // Get weekly payment amount from settings
        $weeklyPaymentAmount = WeeklyPayment::getWeeklyPaymentAmount();
        
        // --- DATA KEUANGAN RILL ---
        $transactions = Transaction::orderBy('date', 'desc')->get();
        $totalIncomeAll = $transactions->where('type', 'income')->sum('amount');
        $totalExpenseAll = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncomeAll - $totalExpenseAll;
        
        // Filter bulan ini
        $monthlyTransactions = Transaction::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
        $monthlyIncome = $monthlyTransactions->where('type', 'income')->sum('amount');
        $monthlyExpense = $monthlyTransactions->where('type', 'expense')->sum('amount');
        
        // --- DATA PEMBAYARAN MINGGUAN ---
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $unpaidAmount = $payments->where('status', 'unpaid')->sum('amount');
        
        // --- RIWAYAT TERBARU ---
        $recentPayments = WeeklyPayment::with('student')
            ->where('status', 'paid')
            ->orderBy('payment_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();
        
        return view('bendahara.dashboard', compact(
            'isWednesday', 
            'currentWeek', 
            'nextWednesday', 
            'currentWeekUnpaid',
            'balance',
            'monthlyIncome',
            'monthlyExpense',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'paidAmount',
            'unpaidAmount',
            'recentPayments'
        ));
    }

    // Manajemen Kas Sederhana
    public function simpleCash()
    {
        $transactions = Transaction::with(['student', 'creator'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        
        return view('bendahara.simple-cash', compact('transactions', 'totalIncome', 'totalExpense', 'balance', 'students'));
    }

    public function storeSimpleTransaction(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:1',
                'description' => 'required|string|max:255',
                'date' => 'required|date',
                'student_id' => 'nullable|exists:users,id'
            ]);

            Log::info('Creating transaction:', [
                'student_id' => $request->student_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'created_by' => auth()->id()
            ]);

            $transaction = Transaction::create([
                'student_id' => $request->student_id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'date' => $request->date,
                'created_by' => auth()->id()
            ]);

            Log::info('Transaction created successfully:', ['transaction_id' => $transaction->id]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan',
                'transaction' => $transaction->load(['student', 'creator'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // API for payment processing
    public function getTransactions()
    {
        try {
            Log::info('getTransactions called');
            
            $transactions = Transaction::with(['student', 'creator'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            Log::info('Transactions fetched: ' . $transactions->count());
            
            $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
            $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
            $balance = $totalIncome - $totalExpense;
            
            $incomeTransactions = $transactions->where('type', 'income');
            Log::info('Income transactions: ' . $incomeTransactions->count());
            Log::info('Payment amount from settings: ' . WeeklyPayment::getWeeklyPaymentAmount());
            
            return response()->json([
                'transactions' => $transactions,
                'summary' => [
                    'totalIncome' => $totalIncome,
                    'totalExpense' => $totalExpense,
                    'balance' => $balance
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTransactions: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to fetch transactions'
            ], 500);
        }
    }

    public function deleteTransaction($id)
    {
        Transaction::findOrFail($id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }

    
    // Pembayaran Mingguan
    public function weeklyPayments(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $currentMonthDate = Carbon::create($year, $month);
        $currentMonthName = $currentMonthDate->locale('id')->translatedFormat('F Y');
        
        // Prev/Next navigation
        $prevMonth = ($month == 1) ? 12 : $month - 1;
        $prevYear = ($month == 1) ? $year - 1 : $year;
        $nextMonth = ($month == 12) ? 1 : $month + 1;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        
        // Sync bills for the selected month (idempotent - create missing, don't duplicate)
        WeeklyPayment::syncMonthlyBills($month, $year);
        
        $payments = WeeklyPayment::with(['student', 'transaction'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('week_number')
            ->orderBy('student_id')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        
        // Get weeks in the selected month (bukan bulan sekarang)
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($month, $year);
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($month, $year);
        
        // Calculate current week ONLY jika viewing current month
        $today = Carbon::now();
        $isCurrentMonth = ($today->month === $month && $today->year === $year);
        $isWednesday = $today->dayOfWeek === 3;
        
        $currentWeek = 1;
        $nextWednesday = null;
        $currentWeekUnpaid = 0;
        
        if ($isCurrentMonth) {
            if (!empty($wednesdayDates)) {
                $firstWednesday = $wednesdayDates[0];
                
                if ($today->lt($firstWednesday)) {
                    $currentWeek = 1;
                } else {
                    // Temukan minggu mana kita berada berdasarkan array Rabu
                    foreach ($wednesdayDates as $index => $wednesday) {
                        if ($today->gte($wednesday)) {
                            $currentWeek = $index + 1;
                        }
                    }
                }
            }
            
            $nextWednesday = $today->copy()->next(Carbon::WEDNESDAY)->format('d M Y');
            
            $currentWeekUnpaid = $payments
                ->where('week_number', $currentWeek)
                ->where('status', 'unpaid')
                ->count();
        }
        
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $unpaidAmount = $payments->where('status', 'unpaid')->sum('amount');
        
        // Get weekly payment amount to display in Blade
        $weeklyPaymentAmount = WeeklyPayment::getWeeklyPaymentAmount();
        
        return view('bendahara.weekly-payments', compact(
            'paymentsByStudent',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'paidAmount',
            'unpaidAmount',
            'isWednesday',
            'isCurrentMonth',
            'currentWeek',
            'nextWednesday',
            'currentWeekUnpaid',
            'month',
            'year',
            'weeksInMonth',
            'wednesdayDates',
            'weeklyPaymentAmount',
            'currentMonthName',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    public function processWeeklyPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:weekly_payments,id',
            'transaction_id' => 'required|exists:transactions,id'
        ]);
        
        $payment = WeeklyPayment::find($request->payment_id);
        $transaction = Transaction::find($request->transaction_id);
        
        $payment->update([
            'status' => 'paid',
            'payment_date' => $transaction->date,
            'transaction_id' => $transaction->id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat'
        ]);
    }

    /**
     * Process arrears - melunasi semua tunggakan siswa di bulan & tahun tertentu
     */
    public function processArrears(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'transaction_id' => 'required|exists:transactions,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        try {
            $transaction = Transaction::findOrFail($request->transaction_id);
            
            // Get all unpaid payments for this student in the specified month/year ONLY
            $unpaidPayments = WeeklyPayment::where('student_id', $request->student_id)
                                        ->where('month', $request->month)
                                        ->where('year', $request->year)
                                        ->where('status', 'unpaid')
                                        ->get();
            
            foreach ($unpaidPayments as $payment) {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $transaction->id,
                    'payment_date' => $transaction->date,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tunggakan berhasil dilunasi!',
                'count' => $unpaidPayments->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Simple Weekly Payments - untuk simple-weekly-payments.blade.php
    public function simpleWeeklyPayments(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $currentMonthDate = Carbon::create($year, $month);
        $currentMonthName = $currentMonthDate->locale('id')->translatedFormat('F Y');
        
        // Prev/Next navigation
        $prevMonth = ($month == 1) ? 12 : $month - 1;
        $prevYear = ($month == 1) ? $year - 1 : $year;
        $nextMonth = ($month == 12) ? 1 : $month + 1;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        
        // Sync bills for selected month (idempotent)
        $this->generateMonthlyBills($month, $year);
        
        $payments = WeeklyPayment::with(['student', 'transaction'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('week_number')
            ->orderBy('student_id')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($month, $year);
        $weeklyPaymentAmount = WeeklyPayment::getWeeklyPaymentAmount();
        
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $unpaidAmount = $payments->where('status', 'unpaid')->sum('amount');
        
        return view('bendahara.simple-weekly-payments', compact(
            'paymentsByStudent',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'weeksInMonth',
            'weeklyPaymentAmount',
            'month',
            'year',
            'currentMonthName',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    // API: Find payment by student, week, month, year
    public function findPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'week_number' => 'required|integer|min:1',  // Dinamis: jumlah minggu bisa berbeda per bulan
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        // Validasi tambahan: week_number tidak boleh melebihi jumlah minggu di bulan tersebut
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($request->month, $request->year);
        if ($request->week_number > $weeksInMonth) {
            return response()->json([
                'success' => false,
                'message' => "Minggu ke-{$request->week_number} tidak ada di bulan tersebut (hanya {$weeksInMonth} minggu)"
            ], 422);
        }

        $payment = WeeklyPayment::where('student_id', $request->student_id)
            ->where('week_number', $request->week_number)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan'
            ], 404);
        }

        // Find latest transaction that can be used (gunakan amount dari settings)
        $amountPerWeek = WeeklyPayment::getWeeklyPaymentAmount();
        $transaction = Transaction::where('type', 'income')
            ->where('amount', $amountPerWeek)
            ->whereNull('weekly_payment_id')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada transaksi yang tersedia. Silahkan input transaksi terlebih dahulu.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Laporan cetak - Halaman utama
     */
    public function laporan()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Get available years from data (last 3 years)
        $years = Transaction::selectRaw('YEAR(date) as year')
            ->union(WeeklyPayment::selectRaw('year'))
            ->distinct()
            ->orderByDesc('year')
            ->limit(5)
            ->pluck('year');
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return view('bendahara.laporan', compact('currentYear', 'currentMonth', 'years', 'months'));
    }

    /**
     * Cetak laporan riwayat keluar masuk uang (Transaction)
     */
    public function cetakKeuangan($month, $year = null)
    {
        $year = $year ?? now()->year;
        
        $monthName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ][$month] ?? 'Tahun Ini';
        
        $transactions = Transaction::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with(['student', 'creator'])
            ->orderBy('date', 'desc')
            ->get();
        
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;
        
        return view('bendahara.laporan-keuangan-cetak', compact(
            'transactions', 'income', 'expense', 'balance', 
            'month', 'year', 'monthName'
        ));
    }

    /**
     * Cetak laporan pembayaran siswa mingguan (WeeklyPayment)
     */
    public function cetakPembayaranSiswa($month, $year = null)
    {
        $year = $year ?? now()->year;
        
        $monthName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ][$month] ?? 'Tahun Ini';
        
        $payments = WeeklyPayment::where('month', $month)
            ->where('year', $year)
            ->with(['student', 'transaction'])
            ->orderBy('student_id')
            ->orderBy('week_number')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        $totalPaid = $payments->where('status', 'paid')->sum('amount');
        $totalBills = $payments->sum('amount');
        
        return view('bendahara.laporan-pembayaran-siswa-cetak', compact(
            'payments', 'paymentsByStudent', 'totalPaid', 'totalBills',
            'month', 'year', 'monthName'
        ));
    }

    /**
     * Laporan Pembayaran - Halaman utama
     */
    public function laporanPembayaran()
    {
        $months = [];
        $now = Carbon::now();
        for ($i = 0; $i < 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $months[] = $date->month;
        }
        $years = [$now->year - 1, $now->year];

        return view('bendahara.laporan-pembayaran', compact('months', 'years'));
    }

    /**
     * Laporan Pembayaran - Cetak
     */
    public function laporanCetak(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020'
        ]);

        $month = $request->month;
        $year = $request->year;

        // Sync bills untuk memastikan data lengkap
        WeeklyPayment::syncMonthlyBills($month, $year);

        $payments = WeeklyPayment::with('student')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('student_id')
            ->orderBy('week_number')
            ->get();

        $paymentsByStudent = $payments->groupBy('student_id');

        $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');

        return view('bendahara.laporan-pembayaran-cetak', compact('paymentsByStudent', 'month', 'year', 'monthName'));
    }

    /**
     * Laporan Pembayaran - PDF Export
     */
    public function laporanPdf(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2030',
            'type' => 'required|in:keuangan,pembayaran'
        ]);

        $month = $request->month;
        $year = $request->year;
        $type = $request->type;

        if ($type === 'keuangan') {
            // PDF Laporan Keuangan
            $transactions = Transaction::with(['student', 'creator'])
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $income = $transactions->where('type', 'income')->sum('amount');
            $expense = $transactions->where('type', 'expense')->sum('amount');
            $balance = $income - $expense;
            
            $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');

            $pdf = Pdf::loadView('bendahara.laporan-keuangan-cetak', compact(
                'transactions', 'income', 'expense', 'balance', 
                'month', 'year', 'monthName'
            ));
            $pdf->setPaper('a4', 'portrait');
            
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="laporan-keuangan-' . $monthName . '.pdf"');
        } else {
            try {
                // Sync bills untuk memastikan data lengkap
                WeeklyPayment::syncMonthlyBills($month, $year);
                
                // PDF Laporan Pembayaran Siswa
                $payments = WeeklyPayment::with(['student', 'transaction'])
                    ->where('month', $month)
                    ->where('year', $year)
                    ->orderBy('student_id')
                    ->orderBy('week_number')
                    ->get();
                    
                // Debug: Check if students are loaded
                foreach($payments as $payment) {
                    if (!$payment->student) {
                        Log::warning('Payment without student: ' . $payment->id);
                    }
                }

                $paymentsByStudent = $payments->groupBy('student_id');
                $totalPaid = $payments->where('status', 'paid')->sum('amount');
                $totalBills = $payments->sum('amount');

                $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');

                // Debug: Log the data
                Log::info('PDF Pembayaran Data:', [
                    'month' => $month,
                    'year' => $year,
                    'payments_count' => $payments->count(),
                    'students_count' => $paymentsByStudent->count(),
                    'totalPaid' => $totalPaid,
                    'totalBills' => $totalBills
                ]);

                $pdf = Pdf::loadView('bendahara.laporan-pembayaran-cetak', compact(
                    'payments', 'paymentsByStudent', 'totalPaid', 'totalBills', 'month', 'year', 'monthName'
                ));
                
                // Configure DomPDF
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions([
                    'defaultFont' => 'Arial',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'dpi' => 150
                ]);
                
                return response($pdf->output())
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="laporan-pembayaran-' . $monthName . '.pdf"');
                    
            } catch (\Exception $e) {
                Log::error('PDF Generation Error: ' . $e->getMessage());
                Log::error('Stack Trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'error' => 'PDF generation failed',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }
    }

    /**
     * Generate monthly bills - menggunakan syncMonthlyBills yang idempotent
     */
    private function generateMonthlyBills($month, $year)
    {
        // Gunakan syncMonthlyBills yang idempotent
        return WeeklyPayment::syncMonthlyBills($month, $year);
    }
}



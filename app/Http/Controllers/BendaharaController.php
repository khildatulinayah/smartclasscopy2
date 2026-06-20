<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\PaymentAdjustment;
use App\Models\WeeklyPayment;
use App\Models\KasSetting;
use App\Services\PaymentAdjustmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class BendaharaController extends Controller
{
    // ============= LAPORAN TAHUNAN (Jan s/d bulan sekarang) =============

    /**
     * Cetak Keuangan Tahunan (HTML)
     */
    public function cetakLaporanTahunanKeuangan($year)
    {
        $year = (int) $year;

        $now = Carbon::now();
        $currentMonth = $now->month;

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, $currentMonth, 1)->endOfMonth()->endOfDay();

        $endMonthName = Carbon::create($year, $currentMonth)->locale('id')->translatedFormat('F');

        // Rekap per bulan
        $monthly = [];
        $incomeTotal = 0;
        $expenseTotal = 0;

        for ($m = 1; $m <= $currentMonth; $m++) {
            $incomeM = (float) Transaction::where('type', 'income')
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');

            $expenseM = (float) Transaction::where('type', 'expense')
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');

            $balanceM = $incomeM - $expenseM;

            $monthNameM = Carbon::create($year, $m)->locale('id')->translatedFormat('F');

            $monthly[] = [
                'monthName' => $monthNameM,
                'income' => $incomeM,
                'expense' => $expenseM,
                'balance' => $balanceM,
            ];

            $incomeTotal += $incomeM;
            $expenseTotal += $expenseM;
        }

        $balanceTotal = $incomeTotal - $expenseTotal;

        return view('bendahara.laporan-keuangan-tahunan-perbulan-cetak', compact(
            'monthly', 'incomeTotal', 'expenseTotal', 'balanceTotal', 'year', 'currentMonth', 'endMonthName'
        ));
    }

    /**
     * PDF Keuangan Tahunan
     */
    public function laporanKeuanganTahunanPdf($year)
    {
        $year = (int) $year;
        $now = Carbon::now();
        $currentMonth = $now->month;

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, $currentMonth, 1)->endOfMonth()->endOfDay();

        $endMonthName = Carbon::create($year, $currentMonth)->locale('id')->translatedFormat('F');

        $transactions = Transaction::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Rekap per bulan
        $monthly = [];
        $incomeTotal = 0;
        $expenseTotal = 0;

        for ($m = 1; $m <= $currentMonth; $m++) {
            $incomeM = (float) Transaction::where('type', 'income')
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');

            $expenseM = (float) Transaction::where('type', 'expense')
                ->whereMonth('date', $m)
                ->whereYear('date', $year)
                ->sum('amount');

            $balanceM = $incomeM - $expenseM;

            $monthNameM = Carbon::create($year, $m)->locale('id')->translatedFormat('F');

            $monthly[] = [
                'monthName' => $monthNameM,
                'income' => $incomeM,
                'expense' => $expenseM,
                'balance' => $balanceM,
            ];

            $incomeTotal += $incomeM;
            $expenseTotal += $expenseM;
        }

        $balanceTotal = $incomeTotal - $expenseTotal;

        $pdf = Pdf::loadView('bendahara.laporan-keuangan-tahunan-perbulan-cetak', compact(
            'monthly', 'incomeTotal', 'expenseTotal', 'balanceTotal', 'year', 'currentMonth', 'endMonthName'
        ));

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'isFontSubsettingEnabled' => true,
            'dpi' => 150
        ]);

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="laporan-keuangan-tahunan-' . $year . '.pdf"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    // ============= DASHBOARD =============
    /**
     * Dashboard - Halaman utama bendahara
     */
    public function dashboard()
    {
        $today = Carbon::now();
        $isWednesday = $today->dayOfWeek === 3;
        
        $currentMonth = $today->month;
        $currentYear = $today->year;
        
        // Dapatkan jumlah minggu dalam bulan ini (dinamis berdasarkan hari Rabu)
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($currentMonth, $currentYear);
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($currentMonth, $currentYear);
        
        // Hitung minggu saat ini berdasarkan hari Rabu aktual
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
        
        // Sinkronkan tagihan bulan ini dengan nominal per bulan/tahun.
        $currentMonthlyNominal = KasSetting::getNominal($currentMonth, $currentYear) ?? 0;
        WeeklyPayment::syncMonthlyBills($currentMonth, $currentYear, $currentMonthlyNominal);
        
        $payments = WeeklyPayment::with(['student', 'transaction', 'adjustment', 'pendingAdjustment'])
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();
        
        // Dapatkan pembayaran minggu ini yang belum dibayar
        $currentWeekUnpaid = $payments
            ->where('week_number', $currentWeek)
            ->where('status', 'unpaid')
            ->count();
        
        // Update atau create nominal kasjalan
        $weeklyPaymentAmount = WeeklyPayment::getWeeklyPaymentAmount($currentMonth, $currentYear);
        
        // Data keuangan
        $transactions = Transaction::orderBy('date', 'desc')->get();
        $totalIncomeAll = $transactions->where('type', 'income')->sum('amount');
        $totalExpenseAll = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncomeAll - $totalExpenseAll;
        
        // Filter untuk bulan ini
        $monthlyTransactions = Transaction::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
        $monthlyIncome = $monthlyTransactions->where('type', 'income')->sum('amount');
        $monthlyExpense = $monthlyTransactions->where('type', 'expense')->sum('amount');
        
        // Statistik pembayaran
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        
        // Hitung unpaidAmount hanya untuk hari Rabu yang sudah lewat
        $unpaidAmount = 0;
        $now = Carbon::now();
        
        foreach($payments as $payment) {
            if ($payment->status === 'unpaid') {
                // Cek tanggal Rabu untuk minggu ini
                $wednesdayDate = isset($wednesdayDates[$payment->week_number - 1]) 
                    ? $wednesdayDates[$payment->week_number - 1] 
                    : null;
                
                // Hanya hitung jika Rabu sudah lewat atau bukan bulan sekarang
                if ($wednesdayDate && ($wednesdayDate->lt($now) || $currentMonth != $now->month || $currentYear != $now->year)) {
                    $unpaidAmount += $payment->amount;
                }
            }
        }
        
        // Riwayat pembayaran terbaru
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

    // ============= MANAJEMEN KAS =============
    
    /**
     * Simple Cash - Halaman manajemen kas
     */
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

    /**
     * Kas Settings - Pengaturan nominal kas
     */
    public function kasSettings()
    {
        $selectedMonth = (int) request()->get('month', now()->month);
        $selectedYear = (int) request()->get('year', now()->year);
        $currentNominal = KasSetting::getNominal($selectedMonth, $selectedYear) ?? 0;

        $isCurrentMonth = $selectedMonth === now()->month && $selectedYear === now()->year;

        return view('bendahara.kas-settings', compact(
            'selectedMonth',
            'selectedYear',
            'currentNominal',
            'isCurrentMonth'
        ));
    }

    /**
     * Update Kas Settings - Simpan nominal kas
     */
    public function updateKasSettings(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2035',
            'nominal' => 'required|numeric|min:0'
        ]);

        KasSetting::updateOrCreate(
            [
                'month' => (int) $request->month,
                'year' => (int) $request->year,
            ],
            ['nominal' => $request->nominal]
        );

        $paymentAdjustmentService = app(PaymentAdjustmentService::class);

        $createdAdjustments = $paymentAdjustmentService->syncAdjustments(
            month: (int) $request->month,
            year: (int) $request->year,
            newNominal: (float) $request->nominal,
            detectedBy: auth()->user(),
        );

        $successMessage = 'Nominal kas berhasil diperbarui.';
        if ($createdAdjustments > 0) {
            $successMessage .= " Terdeteksi {$createdAdjustments} penyesuaian pembayaran yang perlu ditindaklanjuti.";
        } else {
            $successMessage .= ' Tidak ada penyesuaian baru untuk pembayaran yang sudah lunas.';
        }

        return redirect()
            ->route('bendahara.kas.settings', [
                'month' => (int) $request->month,
                'year' => (int) $request->year,
            ])
            ->with('success', $successMessage);
    }

    /**
     * Store Simple Transaction - Simpan transaksi kas
     */
    public function storeSimpleTransaction(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:income,expense',
                'amount' => 'nullable|numeric|min:0',
                'description' => 'required|string|max:255',
                'date' => 'required|date',
                'student_id' => 'nullable|exists:users,id',
                'week_number' => 'nullable|integer|min:1|max:6',
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2020|max:2035',
                'receipt' => 'required_if:type,expense|file|max:2048'
            ]);

            $amount = $request->amount;

            // Ambil nominal dari settings untuk pembayaran mingguan
            if (
                $request->type === 'income' &&
                $request->filled('week_number') &&
                $request->filled('month') &&
                $request->filled('year')
            ) {
                $monthlyNominal = KasSetting::getNominal((int) $request->month, (int) $request->year);

                if ($monthlyNominal === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nominal kas bulan ini belum diatur.'
                    ], 422);
                }

                $amount = $monthlyNominal;
            } elseif ($amount === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal transaksi wajib diisi.'
                ], 422);
            }

            Log::info('Creating transaction:', [
                'student_id' => $request->student_id,
                'type' => $request->type,
                'amount' => $amount,
                'description' => $request->description,
                'date' => $request->date,
                'created_by' => auth()->id()
            ]);

            // Handle receipt upload for expense transactions
            $receiptPath = null;
            if ($request->type === 'expense' && $request->hasFile('receipt')) {
                $receipt = $request->file('receipt');
                
                // Manual validation for file type
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $extension = strtolower($receipt->getClientOriginalExtension());
                
                if (!in_array($extension, $allowedExtensions)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File harus berupa gambar (JPG, JPEG, PNG)'
                    ], 422);
                }
                
                // Additional validation using getimagesize if available
                if (function_exists('getimagesize')) {
                    $imageInfo = @getimagesize($receipt->getPathname());
                    if ($imageInfo === false) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File harus berupa gambar yang valid'
                        ], 422);
                    }
                }
                
                $receiptName = 'receipt_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Create receipts directory if it doesn't exist
                $receiptsDir = public_path('receipts');
                if (!file_exists($receiptsDir)) {
                    mkdir($receiptsDir, 0755, true);
                }
                
                // Move uploaded file manually
                try {
                    $receipt->move($receiptsDir, $receiptName);
                    $receiptPath = 'receipts/' . $receiptName;
                    Log::info('Receipt uploaded:', ['path' => $receiptPath]);
                } catch (\Exception $e) {
                    Log::error('Failed to move uploaded file: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan file: ' . $e->getMessage()
                    ], 500);
                }
            }

            $transaction = Transaction::create([
                'student_id' => $request->student_id,
                'type' => $request->type,
                'amount' => $amount,
                'description' => $request->description,
                'date' => $request->date,
                'created_by' => auth()->id(),
                'receipt_path' => $receiptPath
            ]);

            Log::info('Transaction created successfully:', ['transaction_id' => $transaction->id, 'receipt_path' => $receiptPath]);

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

    // ============= API METHODS =============
    
    /**
     * Get Transactions - API untuk ambil data transaksi
     */
    public function getTransactions()
    {
        try {
            Log::info('getTransactions called');
            
            $transactions = Transaction::with(['student', 'creator'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    $transaction->used_in_weekly_payment = WeeklyPayment::where('transaction_id', $transaction->id)->exists();
                    return $transaction;
                });
            
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

    /**
     * Delete Transaction - Hapus transaksi
     */
    public function deleteTransaction($id)
    {
        Transaction::findOrFail($id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }

    
    // ============= PEMBAYARAN MINGGUAN =============
    
    /**
     * Weekly Payments - Halaman pembayaran mingguan
     */
    public function weeklyPayments(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $currentMonthDate = Carbon::create($year, $month);
        $currentMonthName = $currentMonthDate->locale('id')->translatedFormat('F Y');
        
        // Navigasi Bulan Sebelumnya/Selanjutnya
        $prevMonth = ($month == 1) ? 12 : $month - 1;
        $prevYear = ($month == 1) ? $year - 1 : $year;
        $nextMonth = ($month == 12) ? 1 : $month + 1;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        
        $monthlySetting = KasSetting::getNominal((int) $month, (int) $year);
        $weeklyPaymentAmount = $monthlySetting ?? 0;
        $kasSettingWarning = $monthlySetting === null
            ? 'Nominal kas bulan ini belum diatur. Silakan atur di menu Pengaturan Kas.'
            : null;

        // Sinkronkan tagihan untuk bulan yang dipilih (idempoten - buat yang hilang, jangan duplikasi)
        WeeklyPayment::syncMonthlyBills($month, $year, $weeklyPaymentAmount);

        if ($monthlySetting !== null) {
            $paymentAdjustmentService = app(PaymentAdjustmentService::class);
            $paymentAdjustmentService->reconcileAdjustments(
                month: (int) $month,
                year: (int) $year,
                currentNominal: (float) $weeklyPaymentAmount,
                detectedBy: auth()->user(),
            );
        }
        
        $payments = WeeklyPayment::with(['student', 'transaction', 'adjustment', 'pendingAdjustment'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('week_number')
            ->join('users', 'users.id', '=', 'weekly_payments.student_id')
            ->orderBy('users.name', 'asc')
            ->select('weekly_payments.*')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        
        // Dapatkan jumlah minggu dalam bulan yang dipilih (bukan bulan sekarang)
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($month, $year);
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($month, $year);
        
        // Hitung minggu saat ini HANYA jika melihat bulan sekarang
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
                    // Temukan minggu mana kita berada berdasarkan array hari Rabu
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
        
        $pendingAdjustmentCount = \App\Models\PaymentAdjustment::pending()
            ->whereIn('weekly_payment_id', $payments->pluck('id'))
            ->count();

        // Hitung unpaidAmount hanya untuk hari Rabu yang sudah lewat
        $unpaidAmount = 0;
        $now = Carbon::now();
        $nowStart = $now->copy()->startOfDay();

        foreach ($payments as $payment) {
            if ($payment->status !== 'unpaid') {
                continue;
            }

            // Cek tanggal Rabu untuk minggu ini
            $wednesdayDate = $wednesdayDates[$payment->week_number - 1] ?? null;
            if (!$wednesdayDate) {
                continue;
            }

            // Aturan baru: hanya hitung tunggakan jika tanggal Rabu sudah lewat
            if ($wednesdayDate->copy()->startOfDay()->lt($nowStart)) {
                $unpaidAmount += $payment->amount;
            }
        }
        
        // Load SEMUA adjustment untuk kebutuhan modal (pending + processed + cancelled)
        $pendingAdjustments = \App\Models\PaymentAdjustment::with(['weeklyPayment.student'])
            ->whereIn('weekly_payment_id', $payments->pluck('id'))
            ->get();



        return view('bendahara.weekly-payments', compact(
            'paymentsByStudent',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'paidAmount',
            'unpaidAmount',
            'pendingAdjustmentCount',
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
            'kasSettingWarning',
            'currentMonthName',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    /**
     * Process Weekly Payment - Proses pembayaran mingguan
     */
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
                'amount' => $transaction->amount,
        ]);

        // Pastikan kolom transactions.weekly_payment_id ikut terhubung
        $transaction->weekly_payment_id = $payment->id;
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat'
        ]);
    }

    /**
     * Process Arrears - Lunasi tunggakan siswa
     */
    public function processArrears(Request $request)
    {
        $paymentAdjustmentService = app(\App\Services\PaymentAdjustmentService::class);

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'transaction_id' => 'required|exists:transactions,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        try {
            $transaction = Transaction::findOrFail($request->transaction_id);
            
            // Ambil semua pembayaran belum lunas untuk siswa ini
            $unpaidPayments = WeeklyPayment::where('student_id', $request->student_id)
                                        ->where('month', $request->month)
                                        ->where('year', $request->year)
                                        ->where('status', 'unpaid')
                                        ->get();
            
            foreach ($unpaidPayments as $payment) {
                // Untuk menjaga konsistensi 1-to-1 dengan transactions.weekly_payment_id,
                // buat transaksi per weekly payment (bukan 1 transaksi untuk banyak weekly payment).
                $paymentTransaction = Transaction::create([
                    'student_id' => $payment->student_id,
                    'type' => 'income',
                    'amount' => $payment->amount,
                    'description' => 'Pelunasan tunggakan mingguan',
                    'date' => $transaction->date,
                    'created_by' => auth()->id(),
                ]);

                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $paymentTransaction->id,
                    'payment_date' => $paymentTransaction->date,
                ]);

                // Link transactions.weekly_payment_id
                $paymentTransaction->update([
                    'weekly_payment_id' => $payment->id,
                ]);

                // Sync adjustment shortage yang pending menjadi processed saat lunasi
                $adjustment = $payment->adjustment()->where('status', 'pending')->first();
                if ($adjustment && $adjustment->isShortage()) {
                    $paymentAdjustmentService->processShortageAsUnpaid(
                        adjustment: $adjustment,
                        processedBy: auth()->user(),
                    );
                }
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

    /**
     * Process Shortage Adjustment - Lunasi kekurangan melalui invoice
     */
    public function processShortage(PaymentAdjustment $adjustment)
    {
        $transaction = Transaction::create([
            'student_id' => $adjustment->student_id,
            'type' => 'income',
            'amount' => $adjustment->adjustment_amount,
            'description' => 'Pelunasan kekurangan kas',
            'date' => now(),
            'created_by' => auth()->id(),
        ]);

        $adjustment->update([
            'invoice_transaction_id' => $transaction->id,
        ]);

        $adjustment->markAsProcessed(auth()->user());

        return back()->with('success', 'Kekurangan berhasil dilunasi');
    }

    /**
     * Process Refund Adjustment - Kembalikan kelebihan dana
     */
    public function processRefund(PaymentAdjustment $adjustment)
    {
        $transaction = Transaction::create([
            'student_id' => $adjustment->student_id,
            'type' => 'expense',
            'amount' => abs($adjustment->adjustment_amount),
            'description' => 'Pengembalian kelebihan kas',
            'date' => now(),
            'created_by' => auth()->id(),
        ]);

        $adjustment->update([
            'refund_transaction_id' => $transaction->id,
        ]);

        $adjustment->markAsProcessed(auth()->user());

        return back()->with('success', 'Pengembalian berhasil diproses');
    }

    /**
     * Simple Weekly Payments - Halaman pembayaran sederhana
     */
    public function simpleWeeklyPayments(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $currentMonthDate = Carbon::create($year, $month);
        $currentMonthName = $currentMonthDate->locale('id')->translatedFormat('F Y');
        
        // Navigasi Bulan Sebelumnya/Selanjutnya
        $prevMonth = ($month == 1) ? 12 : $month - 1;
        $prevYear = ($month == 1) ? $year - 1 : $year;
        $nextMonth = ($month == 12) ? 1 : $month + 1;
        $nextYear = ($month == 12) ? $year + 1 : $year;
        
        $monthlySetting = KasSetting::getNominal((int) $month, (int) $year);
        $weeklyPaymentAmount = $monthlySetting ?? 0;

        // Sinkronkan tagihan untuk bulan yang dipilih (idempoten)
        WeeklyPayment::syncMonthlyBills($month, $year, $weeklyPaymentAmount);

        if ($monthlySetting !== null) {
            $paymentAdjustmentService = app(PaymentAdjustmentService::class);
            $paymentAdjustmentService->reconcileAdjustments(
                month: (int) $month,
                year: (int) $year,
                currentNominal: (float) $weeklyPaymentAmount,
                detectedBy: auth()->user(),
            );
        }
        
        $payments = WeeklyPayment::with(['student', 'transaction', 'adjustment', 'pendingAdjustment'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('week_number')
            ->join('users', 'users.id', '=', 'weekly_payments.student_id')
            ->orderBy('users.name', 'asc')
            ->select('weekly_payments.*')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($month, $year);
        $kasSettingWarning = $monthlySetting === null
            ? 'Nominal kas bulan ini belum diatur. Silakan atur di menu Pengaturan Kas.'
            : null;
        
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $pendingAdjustmentCount = \App\Models\PaymentAdjustment::pending()
            ->whereIn('weekly_payment_id', $payments->pluck('id'))
            ->count();
        
        // Hitung unpaidAmount hanya untuk hari Rabu yang sudah lewat
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($month, $year);
        $unpaidAmount = 0;
        $now = Carbon::now();
        
        foreach($payments as $payment) {
            if ($payment->status === 'unpaid') {
                // Cek tanggal Rabu untuk minggu ini
                $wednesdayDate = isset($wednesdayDates[$payment->week_number - 1]) 
                    ? $wednesdayDates[$payment->week_number - 1] 
                    : null;
                
                // Hanya hitung jika Rabu sudah lewat atau bukan bulan sekarang
                if ($wednesdayDate && ($wednesdayDate->lt($now) || $month != $now->month || $year != $now->year)) {
                    $unpaidAmount += $payment->amount;
                }
            }
        }
        
        return view('bendahara.simple-weekly-payments', compact(
            'paymentsByStudent',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'pendingAdjustmentCount',
            'weeksInMonth',
            'weeklyPaymentAmount',
            'kasSettingWarning',
            'month',
            'year',
            'currentMonthName',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    /**
     * Find Payment - API cari pembayaran
     */
    public function findPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'week_number' => 'required|integer|min:1',  // Dinamis: jumlah minggu bisa berbeda per bulan
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'transaction_id' => 'nullable|exists:transactions,id'
        ]);

        // Validasi: minggu tidak boleh melebihi jumlah minggu di bulan
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

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'transaction_id' => $request->transaction_id
        ]);
    }

    // ============= LAPORAN =============
    
    /**
     * Laporan - Halaman utama laporan
     */
    public function laporan()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        // Ambil tahun tersedia dari data
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
     * Cetak Keuangan - Cetak laporan keuangan
     */
    public function cetakKeuangan($month, $year = null)
    {
        $year = $year ?? now()->year;

        $monthName = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ][$month] ?? 'Tahun Ini';

        // Transaksi untuk bagian pengeluaran (dan ringkasan saldo)
        $transactions = Transaction::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with(['student', 'creator'])
            ->orderBy('date', 'desc')
            ->get();

        $income = (float) $transactions->where('type', 'income')->sum('amount');
        $expense = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Data pemasukan dari kas siswa per minggu: ambil dari weekly_payments
        // (akurat per minggu sesuai week_number di database).
        $weeklyPaymentsIncome = WeeklyPayment::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->get()
            ->groupBy('week_number');

        $incomeRows = collect(range(1, 6))
            ->map(function ($w) use ($weeklyPaymentsIncome) {
                $rows = $weeklyPaymentsIncome->get($w, collect());

                if ($rows->isEmpty()) {
                    return null;
                }

                $totalAmount = (float) $rows->sum('amount');
                $studentCount = (int) $rows->pluck('student_id')->unique()->count();

                // Tanggal label: ambil payment_date terkecil/terawal untuk minggu itu.
                $firstDate = $rows->sortBy('payment_date')->first()?->payment_date;
                $label = $firstDate
                    ? Carbon::parse($firstDate)->locale('id')->translatedFormat('d F Y')
                    : ('Minggu ke-' . $w);

                return [
                    'label' => $label,
                    'week' => $w,
                    'amount' => $totalAmount,
                    'student_count' => $studentCount,
                    'per_student_amount' => $studentCount > 0 ? ($totalAmount / $studentCount) : 0,
                ];
            })
            ->filter(fn ($r) => $r && ($r['amount'] ?? 0) != 0)
            ->values();

        return view('bendahara.laporan-keuangan-cetak', compact(
            'transactions', 'income', 'expense', 'balance',
            'month', 'year', 'monthName',
            'incomeRows'
        ));
    }


    /**
     * Cetak Pembayaran Siswa - Cetak laporan pembayaran
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
            ->orderBy('week_number')
            ->get()
            ->sortBy(function ($payment) {
                return strtolower(trim(optional($payment->student)->name ?? ''));
            });
        
        $paymentsByStudent = $payments->groupBy('student_id');
        $totalPaid = $payments->where('status', 'paid')->sum('amount');
        $totalBills = $payments->sum('amount');
        
        return view('bendahara.laporan-pembayaran-siswa-cetak', compact(
            'payments', 'paymentsByStudent', 'totalPaid', 'totalBills',
            'month', 'year', 'monthName'
        ));
    }

    /**
     * Laporan Pembayaran - Halaman laporan pembayaran
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
     * Laporan Cetak - Cetak laporan pembayaran
     */
    public function laporanCetak(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020'
        ]);

        $month = $request->month;
        $year = $request->year;

        // Sinkronkan tagihan untuk data lengkap
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

    // ============= PDF EXPORT =============
    
    /**
     * PDF Keuangan - Export laporan keuangan ke PDF
     */
    public function laporanKeuanganPdf($month, $year = null)
    {
        $year = $year ?? now()->year;
        
        // Data untuk PDF keuangan
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
        $pdf->setPaper('a4', 'landscape'); // Landscape untuk format bendahara
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'Arial',
            'isFontSubsettingEnabled' => true,
            'dpi' => 150
        ]);
        
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="laporan-keuangan-' . $monthName . '.pdf"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * PDF Pembayaran Siswa - Export laporan pembayaran ke PDF
     */
    public function laporanPembayaranPdf($month, $year = null)
    {
        $year = $year ?? now()->year;
        
        try {

            // Cek autentikasi user untuk PDF
            if (!auth()->check()) {
                // Coba dapatkan user default untuk pembuatan PDF
                $defaultUser = \App\Models\User::where('role', 'bendahara')->first();
                if ($defaultUser) {
                    auth()->login($defaultUser);
                }
            }
            
            // Sinkronkan tagihan untuk data PDF lengkap
            WeeklyPayment::syncMonthlyBills($month, $year);
            
            // Data untuk PDF pembayaran
            $payments = WeeklyPayment::with(['student', 'transaction', 'adjustment', 'pendingAdjustment'])
                ->where('month', $month)
                ->where('year', $year)
                ->orderBy('student_id')
                ->orderBy('week_number')
                ->get();
                
            // Debug: cek relasi siswa
            foreach($payments as $payment) {
                if (!$payment->student) {
                    Log::warning('Payment without student: ' . $payment->id);
                }
            }

            $paymentsByStudent = $payments->groupBy('student_id');
            $totalPaid = $payments->where('status', 'paid')->sum('amount');
            $totalBills = $payments->sum('amount');

            $monthName = Carbon::create($year, $month)->locale('id')->translatedFormat('F Y');

            // Info user yang login
            $userName = auth()->check() ? auth()->user()->name : 'System';
            $userRole = auth()->check() ? ucfirst(auth()->user()->role) : 'Administrator';

            // Debug: log data PDF
            Log::info('PDF Pembayaran Data:', [
                'month' => $month,
                'year' => $year,
                'payments_count' => $payments->count(),
                'students_count' => $paymentsByStudent->count(),
                'totalPaid' => $totalPaid,
                'totalBills' => $totalBills,
                'user_authenticated' => auth()->check(),
                'user_name' => $userName
            ]);

            $pdf = Pdf::loadView('bendahara.laporan-pembayaran-siswa-cetak', compact(
                'payments', 'paymentsByStudent', 'totalPaid', 'totalBills', 'month', 'year', 'monthName', 'userName', 'userRole'
            ));
            
            // Konfigurasi DomPDF
            $pdf->setPaper('a4', 'landscape'); // Landscape untuk format bendahara
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'isFontSubsettingEnabled' => true,
                'dpi' => 150
            ]);
            
            return response($pdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="laporan-pembayaran-' . $monthName . '.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
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
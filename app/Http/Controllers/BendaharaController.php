<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BendaharaController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::now();
        $isWednesday = $today->dayOfWeek === 3;
        
        // Perbaikan: hitung week number berdasarkan hari Rabu, bukan weekOfMonth
        $startOfMonth = $today->copy()->startOfMonth();
        $firstWednesday = $startOfMonth->copy()->next(Carbon::WEDNESDAY);
        
        // Jika hari ini sebelum Rabu pertama bulan ini, pakai minggu 1
        if ($today->lt($firstWednesday)) {
            $currentWeek = 1;
        } else {
            // Hitung selisih hari dari Rabu pertama, dibagi 7, +1
            $daysSinceFirstWednesday = $today->diffInDays($firstWednesday);
            $currentWeek = min(4, intval($daysSinceFirstWednesday / 7) + 1);
        }
        
        $nextWednesday = $today->copy()->next(Carbon::WEDNESDAY)->format('d M Y');
        
        $currentMonth = $today->month;
        $currentYear = $today->year;
        
        // Generate tagihan jika belum ada
        $this->generateMonthlyBills($currentMonth, $currentYear);
        
        $payments = WeeklyPayment::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();
        
        // Perbaikan: filter berdasarkan minggu aktif yang sudah lewat
        $currentWeekUnpaid = $payments
            ->where('week_number', $currentWeek)
            ->where('status', 'unpaid')
            ->count();
        
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
        $transactions = Transaction::with(['student', 'creator'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $balance
            ]
        ]);
    }

    public function deleteTransaction($id)
    {
        Transaction::findOrFail($id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }

    // Daftar Siswa
    public function studentList()
    {
        $students = User::where('role', 'siswa')->where('is_active', true)->orderBy('name')->get();
        return view('bendahara.student-list', compact('students'));
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
        
        // Always generate bills for the selected month (safe idempotent)
        $this->generateMonthlyBills($month, $year);
        
        $payments = WeeklyPayment::with(['student', 'transaction'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('week_number')
            ->orderBy('student_id')
            ->get();
        
        $paymentsByStudent = $payments->groupBy('student_id');
        
        $today = Carbon::now();
        $isWednesday = $today->dayOfWeek === 3;
        
        // Perbaikan perhitungan minggu aktif
        $startOfMonth = $today->copy()->startOfMonth();
        $firstWednesday = $startOfMonth->copy()->next(Carbon::WEDNESDAY);
        
        if ($today->lt($firstWednesday)) {
            $currentWeek = 1;
        } else {
            $daysSinceFirstWednesday = $today->diffInDays($firstWednesday);
            $currentWeek = min(4, intval($daysSinceFirstWednesday / 7) + 1);
        }
        
        $nextWednesday = $today->copy()->next(Carbon::WEDNESDAY)->format('d M Y');
        
        $currentWeekUnpaid = $payments
            ->where('week_number', $currentWeek)
            ->where('status', 'unpaid')
            ->count();
        
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $unpaidAmount = $payments->where('status', 'unpaid')->sum('amount');
        
        return view('bendahara.weekly-payments', compact(
            'paymentsByStudent',
            'totalStudents',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'totalAmount',
            'paidAmount',
            'unpaidAmount',
            'isWednesday',
            'currentWeek',
            'nextWednesday',
            'currentWeekUnpaid',
            'month',
            'year',
            'currentMonthName',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear'
        ));
    }

    private function generateMonthlyBills($month, $year)
    {
        $existingCount = WeeklyPayment::where('month', $month)->where('year', $year)->count();
        if ($existingCount > 0) {
            return 0;
        }
        
        $students = User::where('role', 'siswa')->where('is_active', true)->get();
        $generatedCount = 0;
        
        foreach ($students as $student) {
            for ($week = 1; $week <= 4; $week++) {
                WeeklyPayment::create([
                    'student_id' => $student->id,
                    'week_number' => $week,
                    'month' => $month,
                    'year' => $year,
                    'amount' => 5000,
                    'status' => 'unpaid',
                    'payment_date' => null,
                    'transaction_id' => null,
                    'created_by' => auth()->id() ?? 1,
                ]);
                $generatedCount++;
            }
        }
        
        return $generatedCount;
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

    public function processArrears(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        try {
            $transaction = Transaction::findOrFail($request->transaction_id);
            
            // Get all unpaid payments for this student
            $unpaidPayments = WeeklyPayment::where('student_id', $request->student_id)
                                        ->where('status', 'unpaid')
                                        ->get();
            
            foreach ($unpaidPayments as $payment) {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $transaction->id,
                    'paid_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tunggakan berhasil dilunasi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    
}



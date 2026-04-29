<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['student', 'creator'])
            ->orderBy('date', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20);
        $transactions->appends($request->query());

        $stats = Transaction::selectRaw('type, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('type')
            ->get();

        return view('admin.finance', compact('transactions', 'stats'));
    }

    public function validateTransaction(Request $request, Transaction $transaction)
    {
        if ($transaction->type !== 'expense') {
            return response()->json(['success' => false, 'message' => 'Hanya pengeluaran yang bisa divalidasi'], 400);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $oldStatus = $transaction->approval_status ?? 'pending';
        $transaction->update(['approval_status' => $request->status]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'validate_transaction',
            'description' => "Validated transaction #{$transaction->id} ({$transaction->description}) status from {$oldStatus} to {$request->status}",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil divalidasi',
            'status' => $request->status
        ]);
    }

    public function detail(Transaction $transaction)
    {
        $transaction->load('student', 'creator');
        return response()->json($transaction);
    }
}


# Payment Form Integration - Credit Balance & Adjustments

## Overview

Dokumentasi ini menjelaskan bagaimana mengintegrasikan sistem payment adjustment ke dalam payment form yang sudah ada.

---

## 1. Payment Form Enhanced

### Scenario: Student dengan Outstanding Adjustment

```html
<!-- Enhanced Payment Form -->
<div class="payment-form">
    <div class="form-section">
        <h5>Pembayaran Kas Mingguan</h5>

        <!-- Student Info -->
        <div class="student-info">
            <p><strong>Siswa:</strong> {{ $student->name }}</p>
            <p><strong>Kelas:</strong> {{ $student->class }}</p>
        </div>

        <!-- Adjustment Alert -->
        @if ($hasPendingAdjustment)
            <div class="alert alert-warning">
                <strong>⚠️ Ada Penyesuaian Pembayaran</strong>
                <p>
                    Pembayaran minggu kemarin dicatat dengan nominal Rp {{ number_format($originalAmount, 0, ',', '.') }},
                    namun nominal yang benar adalah Rp {{ number_format($currentNominal, 0, ',', '.') }}.
                </p>
                <p>
                    <strong>Selisih: Rp {{ number_format($adjustmentAmount, 0, ',', '.') }}</strong>
                </p>
                <p class="text-muted small">
                    Sistem telah membuat penyesuaian otomatis. Anda dapat membayar sekarang untuk melunasi selisihnya.
                </p>
            </div>
        @endif

        <!-- Credit Balance Display -->
        @if ($creditBalance > 0)
            <div class="alert alert-info">
                <strong>💳 Saldo Kredit Tersedia</strong>
                <p>Rp {{ number_format($creditBalance, 0, ',', '.') }}</p>
                <label>
                    <input type="checkbox" id="useCredit" name="use_credit" value="true">
                    Gunakan saldo kredit untuk pembayaran ini
                </label>
            </div>
        @endif

        <!-- Payment Amount Calculation -->
        <div class="form-group">
            <label>Nominal Pembayaran</label>
            <table class="table table-sm">
                <tr>
                    <td>Tagihan:</td>
                    <td class="text-right">
                        Rp <span id="billAmount">{{ number_format($billAmount, 0, ',', '.') }}</span>
                    </td>
                </tr>
                @if ($hasPendingAdjustment)
                    <tr>
                        <td>Penyesuaian:</td>
                        <td class="text-right">
                            Rp <span id="adjustmentAmountDisplay">{{ number_format($adjustmentAmount, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                @endif
                @if ($creditBalance > 0)
                    <tr>
                        <td>Saldo Kredit:</td>
                        <td class="text-right">
                            - Rp <span id="creditUsed">0</span>
                        </td>
                    </tr>
                @endif
                <tr class="table-active">
                    <td><strong>Total Bayar:</strong></td>
                    <td class="text-right">
                        <strong>Rp <span id="totalPayment">{{ number_format($totalPaymentDue, 0, ',', '.') }}</span></strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Payment Method -->
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" value="cash" id="methodCash" checked>
                <label class="form-check-label" for="methodCash">
                    Tunai
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="methodTransfer">
                <label class="form-check-label" for="methodTransfer">
                    Transfer Bank
                </label>
            </div>
        </div>

        <!-- Notes/Evidence -->
        <div class="form-group">
            <label>Catatan (Opsional)</label>
            <textarea class="form-control" name="notes" rows="3" placeholder="Catatan pembayaran..."></textarea>
        </div>

        <!-- Receipt Upload -->
        <div class="form-group">
            <label>Upload Bukti (Opsional)</label>
            <input type="file" class="form-control" name="receipt" accept="image/*">
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
    </div>
</div>
```

---

## 2. Backend: Process Payment with Credit & Adjustment

### Service untuk Handle Payment

```php
<?php

namespace App\Services;

use App\Models\StudentCreditBalance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\PaymentAdjustment;
use Illuminate\Support\Facades\DB;

class PaymentProcessingService
{
    /**
     * Process student payment dengan automatic credit usage & adjustment
     */
    public function processPayment(
        WeeklyPayment $weeklyPayment,
        float $paymentAmount,
        bool $useCredit = false,
        ?string $paymentMethod = 'cash',
        ?string $notes = null,
        ?string $receiptPath = null
    ): Transaction {
        return DB::transaction(function () use (
            $weeklyPayment,
            $paymentAmount,
            $useCredit,
            $paymentMethod,
            $notes,
            $receiptPath
        ) {
            $studentId = $weeklyPayment->student_id;
            $totalDue = $this->calculateTotalDue($weeklyPayment);
            $creditUsed = 0;

            // Step 1: Use credit jika diminta dan tersedia
            if ($useCredit && $totalDue > $paymentAmount) {
                $creditBalance = StudentCreditBalance::forStudent(
                    $weeklyPayment->student
                );

                $creditNeeded = $totalDue - $paymentAmount;
                $creditAvailable = (float) $creditBalance->total_credit;

                if ($creditAvailable > 0) {
                    $creditUsed = min($creditNeeded, $creditAvailable);
                    $creditBalance->useCredit($creditUsed);
                }
            }

            // Step 2: Create transaction untuk pembayaran
            $transaction = Transaction::create([
                'student_id' => $studentId,
                'type' => 'income',
                'amount' => $paymentAmount,
                'description' => "Pembayaran kas mingguan: Minggu {$weeklyPayment->week_number}",
                'date' => now()->toDateString(),
                'created_by' => auth()->id(),
                'receipt_path' => $receiptPath,
            ]);

            // Step 3: Update weekly payment
            $weeklyPayment->update([
                'status' => 'paid',
                'payment_date' => now(),
                'transaction_id' => $transaction->id,
            ]);

            // Step 4: Process adjustment jika ada
            if ($weeklyPayment->hasAdjustment()) {
                $this->processAdjustmentPayment(
                    adjustment: $weeklyPayment->adjustment,
                    paymentAmount: $paymentAmount,
                    creditUsed: $creditUsed
                );
            }

            return $transaction;
        });
    }

    /**
     * Calculate total due untuk weekly payment (incl. adjustment)
     */
    public function calculateTotalDue(WeeklyPayment $weeklyPayment): float
    {
        $baseDue = (float) $weeklyPayment->amount;

        // Tambah adjustment jika ada shortage
        if ($weeklyPayment->hasAdjustment()) {
            $adjustment = $weeklyPayment->adjustment;
            if ($adjustment->isShortage() && $adjustment->status === 'pending') {
                $baseDue += abs((float) $adjustment->adjustment_amount);
            }
        }

        return $baseDue;
    }

    /**
     * Handle adjustment payment
     */
    private function processAdjustmentPayment(
        PaymentAdjustment $adjustment,
        float $paymentAmount,
        float $creditUsed
    ): void {
        if (!$adjustment->isShortage()) {
            return;
        }

        $adjustmentAmount = abs((float) $adjustment->adjustment_amount);
        $baseBill = (float) $adjustment->weeklyPayment->amount;

        // Jika payment >= baseBill + adjustment, maka lunasi adjustment
        if ($paymentAmount >= $baseBill + $adjustmentAmount || $creditUsed >= $adjustmentAmount) {
            $adjustment->markAsProcessed(
                processedBy: auth()->user(),
                notes: "Dibayar bersama pembayaran mingguan"
            );
        }
    }

    /**
     * Get payment detail untuk display
     */
    public function getPaymentDetail(WeeklyPayment $weeklyPayment): array
    {
        $credit = StudentCreditBalance::forStudent($weeklyPayment->student);
        $totalDue = $this->calculateTotalDue($weeklyPayment);

        return [
            'bill_amount' => $weeklyPayment->amount,
            'has_adjustment' => $weeklyPayment->hasAdjustment(),
            'adjustment_amount' => $weeklyPayment->hasAdjustment()
                ? abs((float) $weeklyPayment->adjustment->adjustment_amount)
                : 0,
            'adjustment_type' => $weeklyPayment->adjustment?->adjustment_type_label,
            'credit_available' => $credit->total_credit,
            'total_due' => $totalDue,
            'can_use_credit' => (float) $credit->total_credit > 0,
        ];
    }
}
```

### Controller Update

```php
<?php

namespace App\Http\Controllers;

use App\Models\WeeklyPayment;
use App\Services\PaymentProcessingService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private PaymentProcessingService $paymentService;

    public function __construct(PaymentProcessingService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment form dengan credit & adjustment info
     */
    public function showPaymentForm(WeeklyPayment $payment)
    {
        return view('payment.form', [
            'payment' => $payment,
            'detail' => $this->paymentService->getPaymentDetail($payment),
        ]);
    }

    /**
     * POST untuk process payment
     */
    public function processPayment(Request $request, WeeklyPayment $payment)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'use_credit' => 'boolean',
            'payment_method' => 'in:cash,transfer',
            'notes' => 'nullable|string|max:500',
            'receipt' => 'nullable|image|max:2048',
        ]);

        try {
            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
            }

            $transaction = $this->paymentService->processPayment(
                weeklyPayment: $payment,
                paymentAmount: (float) $request->payment_amount,
                useCredit: (bool) $request->use_credit,
                paymentMethod: $request->payment_method,
                notes: $request->notes,
                receiptPath: $receiptPath
            );

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'transaction_id' => $transaction->id,
                'redirect' => route('payment.receipt', $transaction),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }
}
```

---

## 3. JavaScript untuk Form Calculation

```javascript
// Payment Form Calculator
class PaymentCalculator {
    constructor() {
        this.billAmount = parseFloat(document.getElementById('billAmount').textContent.replace(/\D/g, ''));
        this.adjustmentAmount = document.getElementById('adjustmentAmountDisplay')
            ? parseFloat(document.getElementById('adjustmentAmountDisplay').textContent.replace(/\D/g, ''))
            : 0;
        this.creditAvailable = document.getElementById('creditAvailable')
            ? parseFloat(document.getElementById('creditAvailable').textContent.replace(/\D/g, ''))
            : 0;
        
        this.init();
    }

    init() {
        // Listen to checkbox changes
        const useCredit = document.getElementById('useCredit');
        if (useCredit) {
            useCredit.addEventListener('change', () => this.calculate());
        }

        // Listen to payment amount changes
        const paymentInput = document.getElementById('paymentAmount');
        if (paymentInput) {
            paymentInput.addEventListener('input', () => this.calculate());
        }

        this.calculate();
    }

    calculate() {
        const useCredit = document.getElementById('useCredit')?.checked || false;
        const paymentAmount = parseFloat(document.getElementById('paymentAmount')?.value || 0);
        
        let totalDue = this.billAmount + this.adjustmentAmount;
        let creditUsed = 0;

        if (useCredit && this.creditAvailable > 0) {
            creditUsed = Math.min(
                this.creditAvailable,
                Math.max(0, totalDue - paymentAmount)
            );
        }

        const finalTotal = paymentAmount + creditUsed;

        // Update display
        if (document.getElementById('creditUsed')) {
            document.getElementById('creditUsed').textContent = 
                this.formatCurrency(creditUsed);
        }

        if (document.getElementById('totalPayment')) {
            document.getElementById('totalPayment').textContent = 
                this.formatCurrency(finalTotal);
        }

        // Show warning jika pembayaran kurang
        this.showPaymentWarning(paymentAmount, totalDue);
    }

    showPaymentWarning(paid, due) {
        const warningDiv = document.getElementById('paymentWarning');
        if (!warningDiv) return;

        if (paid < due) {
            const remaining = due - paid;
            warningDiv.innerHTML = `
                <div class="alert alert-warning">
                    Pembayaran kurang: Rp ${this.formatCurrency(remaining)}
                </div>
            `;
        } else {
            warningDiv.innerHTML = '';
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID').format(Math.round(amount));
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    new PaymentCalculator();
});
```

---

## 4. Blade Template - Payment Form

```blade
<!-- resources/views/payment/form.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💳 Form Pembayaran Kas Mingguan</h5>
                </div>
                <div class="card-body">
                    <!-- Student Info -->
                    <div class="alert alert-light">
                        <p>
                            <strong>Siswa:</strong> {{ $payment->student->name }}<br>
                            <strong>Minggu:</strong> {{ $payment->week_number }} / {{ $payment->month }}/{{ $payment->year }}
                        </p>
                    </div>

                    <!-- Adjustment Alert -->
                    @if ($detail['has_adjustment'])
                        <div class="alert alert-warning">
                            <strong>⚠️ Ada Penyesuaian Pembayaran</strong>
                            <p class="mb-1">
                                Tipe: <span class="badge badge-warning">{{ $detail['adjustment_type'] }}</span>
                            </p>
                            <p class="mb-0">
                                Jumlah: Rp {{ number_format($detail['adjustment_amount'], 0, ',', '.') }}
                            </p>
                        </div>
                    @endif

                    <!-- Credit Alert -->
                    @if ($detail['can_use_credit'])
                        <div class="alert alert-info">
                            <strong>💳 Saldo Kredit Tersedia</strong>
                            <p class="mb-0">
                                Rp {{ number_format($detail['credit_available'], 0, ',', '.') }}
                            </p>
                            <label class="mt-2 mb-0">
                                <input type="checkbox" id="useCredit" name="use_credit">
                                Gunakan saldo kredit
                            </label>
                        </div>
                    @endif

                    <!-- Payment Calculation Table -->
                    <table class="table table-sm mb-4">
                        <tr>
                            <td>Tagihan Mingguan:</td>
                            <td class="text-right">
                                Rp <span id="billAmount">{{ number_format($detail['bill_amount'], 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @if ($detail['has_adjustment'])
                            <tr>
                                <td>Penyesuaian:</td>
                                <td class="text-right">
                                    + Rp <span id="adjustmentAmountDisplay">{{ number_format($detail['adjustment_amount'], 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endif
                        @if ($detail['can_use_credit'])
                            <tr>
                                <td>Saldo Kredit:</td>
                                <td class="text-right">
                                    - Rp <span id="creditUsed">0</span>
                                </td>
                            </tr>
                        @endif
                        <tr class="table-active">
                            <td><strong>Total Bayar:</strong></td>
                            <td class="text-right">
                                <strong>Rp <span id="totalPayment">{{ number_format($detail['total_due'], 0, ',', '.') }}</span></strong>
                            </td>
                        </tr>
                    </table>

                    <!-- Payment Form -->
                    <form id="paymentForm" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="paymentAmount">Jumlah Bayar (Rp)</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="paymentAmount"
                                name="payment_amount" 
                                step="100"
                                min="0"
                                value="{{ $detail['bill_amount'] }}"
                                required
                            >
                            <small class="form-text text-muted">
                                Total yang harus dibayar: Rp {{ number_format($detail['total_due'], 0, ',', '.') }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="paymentMethod">Metode Pembayaran</label>
                            <select class="form-control" name="payment_method" required>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan (Opsional)</label>
                            <textarea 
                                class="form-control" 
                                name="notes" 
                                rows="2"
                                placeholder="Catatan pembayaran..."
                            ></textarea>
                        </div>

                        <div class="form-group">
                            <label for="receipt">Upload Bukti (Opsional)</label>
                            <input 
                                type="file" 
                                class="form-control-file" 
                                name="receipt"
                                accept="image/*"
                            >
                        </div>

                        <div id="paymentWarning"></div>

                        <button type="submit" class="btn btn-primary btn-block mt-4">
                            Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Hidden fields untuk calculator
document.body.innerHTML += `
    <input type="hidden" id="adjustmentAmount" value="{{ $detail['has_adjustment'] ? $detail['adjustment_amount'] : 0 }}">
    <input type="hidden" id="creditAvailable" value="{{ $detail['can_use_credit'] ? $detail['credit_available'] : 0 }}">
`;

// Load calculator
@include('payment._calculator_script')
</script>
@endsection
```

---

## 5. Best Practices

### DO ✅
- Selalu check `hasAdjustment()` sebelum show adjustment info
- Calculate total due secara dinamis dari database
- Log semua payment transactions
- Verify payment amount vs due amount

### DON'T ❌
- Jangan hardcode nominal (ambil dari KasSetting)
- Jangan update weekly_payment.amount saat process (immutable)
- Jangan gunakan credit tanpa explicit checkbox
- Jangan allow overpayment tanpa clear reason

---

## 6. Testing Scenarios

### Scenario 1: Student Pays with Shortage Adjustment

```php
// Weekly payment: Rp 6000, sudah paid
$payment = WeeklyPayment::find(1); // amount=6000, status='paid'

// KasSetting berubah: Rp 8000
KasSetting::updateOrCreate([...], ['nominal' => 8000]);

// PaymentAdjustment dibuat
$adjustment = PaymentAdjustment::where('weekly_payment_id', 1)->first();
// -> adjustment_type = 'shortage'
// -> adjustment_amount = 2000
// -> status = 'pending'

// Student bayar tahap 2: Rp 8000 (untuk minggu berikutnya)
// Form akan show: Tagihan Minggu 2 (Rp 8000) + Adjustment Minggu 1 (Rp 2000) = Total Rp 10000

$paymentService->processPayment(
    weeklyPayment: $nextWeekPayment,
    paymentAmount: 10000,
    useCredit: false
);

// Result:
// - Minggu 2 payment marked as paid
// - Adjustment status → 'processed'
```

### Scenario 2: Student Uses Credit

```php
// Student punya kredit Rp 5000
$creditBalance = StudentCreditBalance::forStudent($student); // total_credit = 5000

// Bayar dengan kredit
$paymentService->processPayment(
    weeklyPayment: $payment,
    paymentAmount: 3000,
    useCredit: true
);

// Result:
// - Credit used: Rp 2000 (to make up Rp 5000 total)
// - Credit balance: Rp 3000 (remaining)
// - Payment marked as paid
```

---

## 7. Related Routes

```php
Route::post('/payment/{weeklyPayment}/process', 
    [PaymentController::class, 'processPayment'])
    ->name('payment.process');

Route::get('/payment/{weeklyPayment}/form', 
    [PaymentController::class, 'showPaymentForm'])
    ->name('payment.form');

Route::get('/payment/{transaction}/receipt', 
    [PaymentController::class, 'showReceipt'])
    ->name('payment.receipt');
```

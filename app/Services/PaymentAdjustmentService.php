<?php

namespace App\Services;

use App\Models\PaymentAdjustment;
use App\Models\StudentCreditBalance;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WeeklyPayment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengelola Payment Adjustment
 * 
 * Tanggung jawab:
 * - Deteksi perbedaan nominal pembayaran
 * - Create adjustment records
 * - Process adjustment dengan berbagai handling methods
 * - Manage credit balance siswa
 * 
 * Clean Architecture:
 * - Semua business logic di service ini
 * - Controller hanya call method di service
 * - Model hanya handle relationship & accessor
 */
class PaymentAdjustmentService
{
    /**
     * Deteksi dan create adjustment untuk semua pembayaran yang affected
     * 
     * Dipanggil ketika nominal kas berubah di KasSetting
     * 
     * @param int $month
     * @param int $year
     * @param float $newNominal Nominal baru
     * @param float $oldNominal Nominal lama
     * @param User $detectedBy User yang mendeteksi
     * @return Collection Adjustment yang dibuat
     */
    public function detectAndCreateAdjustments(
        int $month,
        int $year,
        float $newNominal,
        float $oldNominal,
        User $detectedBy
    ): Collection {
        // Jika nominal tidak berubah, jangan create adjustment
        if ($newNominal == $oldNominal) {
            return new Collection();
        }

        return DB::transaction(function () use ($month, $year, $newNominal, $oldNominal, $detectedBy) {
            // Cari semua weekly payment yang sudah dibayar pada bulan/tahun tersebut
            $paidPayments = WeeklyPayment::where('month', $month)
                ->where('year', $year)
                ->where('status', 'paid')
                ->get();

            $adjustments = collect([]);

            foreach ($paidPayments as $payment) {
                $difference = $newNominal - $payment->amount;

                $existingAdjustment = PaymentAdjustment::where('weekly_payment_id', $payment->id)->first();

                if ($difference == 0) {
                    if ($existingAdjustment && $existingAdjustment->status === 'pending') {
                        $existingAdjustment->delete();
                    }
                    continue;
                }

                $adjustmentType = $difference > 0 ? 'shortage' : 'overpayment';
                $adjustmentAmount = abs($difference);

                if ($existingAdjustment) {
                    if ($existingAdjustment->status === 'pending') {
                        $existingAdjustment->delete();
                        $existingAdjustment = null;
                    } else {
                        $shouldUpdate = (
                            (float) $existingAdjustment->current_nominal !== (float) $newNominal ||
                            (float) $existingAdjustment->adjustment_amount !== $adjustmentAmount ||
                            $existingAdjustment->adjustment_type !== $adjustmentType
                        );

                        if (!$shouldUpdate) {
                            continue;
                        }

                        $existingAdjustment->update([
                            'original_amount' => $payment->amount,
                            'current_nominal' => $newNominal,
                            'adjustment_amount' => $adjustmentAmount,
                            'adjustment_type' => $adjustmentType,
                            'handling_method' => $adjustmentType === 'shortage' ? 'invoice' : 'refund',
                            'status' => 'pending',
                            'invoice_transaction_id' => null,
                            'refund_transaction_id' => null,
                            'credit_transaction_id' => null,
                            'processed_by' => null,
                            'processed_at' => null,
                            'notes' => null,
                            'detected_by' => $detectedBy->id,
                        ]);

                        $adjustments->push($existingAdjustment);
                        continue;
                    }
                }

                $adjustment = $this->createAdjustment(
                    weeklyPayment: $payment,
                    originalAmount: $payment->amount,
                    currentNominal: $newNominal,
                    detectedBy: $detectedBy
                );

                if ($adjustment) {
                    $adjustments->push($adjustment);
                }
            }

            return $adjustments;
        });
    }

    /**
     * Sync adjustment records untuk semua pembayaran yang sudah dibayar ketika nominal berubah
     *
     * @param int $month
     * @param int $year
     * @param float $newNominal
     * @param User $detectedBy
     * @return int Jumlah adjustment yang dibuat
     */
    public function syncAdjustments(
        int $month,
        int $year,
        float $newNominal,
        User $detectedBy
    ): int {
        return DB::transaction(function () use ($month, $year, $newNominal, $detectedBy) {
            $payments = WeeklyPayment::where('month', $month)
                ->where('year', $year)
                ->where('status', 'paid')
                ->get();

            $createdCount = 0;

            foreach ($payments as $payment) {
                $difference = $newNominal - $payment->amount;

                if ($difference == 0) {
                    $existingAdjustment = PaymentAdjustment::where('weekly_payment_id', $payment->id)->first();
                    if ($existingAdjustment && $existingAdjustment->status === 'pending') {
                        $existingAdjustment->delete();
                    }
                    continue;
                }

                $existingAdjustment = PaymentAdjustment::where('weekly_payment_id', $payment->id)->first();
                $adjustmentAmount = abs($difference);
                $adjustmentType = $difference > 0 ? 'shortage' : 'overpayment';
                $handlingMethod = $adjustmentType === 'shortage' ? 'invoice' : 'refund';

                if ($existingAdjustment) {
                    if ($existingAdjustment->status === 'pending') {
                        $existingAdjustment->delete();
                        $existingAdjustment = null;
                    } else {
                        $shouldUpdate = (
                            (float) $existingAdjustment->current_nominal !== (float) $newNominal ||
                            (float) $existingAdjustment->adjustment_amount !== $adjustmentAmount ||
                            $existingAdjustment->adjustment_type !== $adjustmentType
                        );

                        if (!$shouldUpdate) {
                            continue;
                        }

                        $existingAdjustment->update([
                            'original_amount' => $payment->amount,
                            'current_nominal' => $newNominal,
                            'adjustment_amount' => $adjustmentAmount,
                            'adjustment_type' => $adjustmentType,
                            'handling_method' => $handlingMethod,
                            'status' => 'pending',
                            'invoice_transaction_id' => null,
                            'refund_transaction_id' => null,
                            'credit_transaction_id' => null,
                            'processed_by' => null,
                            'processed_at' => null,
                            'notes' => null,
                            'detected_by' => $detectedBy->id,
                        ]);

                        $createdCount++;
                        continue;
                    }
                }

                PaymentAdjustment::create([
                    'weekly_payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'original_amount' => $payment->amount,
                    'current_nominal' => $newNominal,
                    'adjustment_amount' => $adjustmentAmount,
                    'adjustment_type' => $adjustmentType,
                    'handling_method' => $handlingMethod,
                    'status' => 'pending',
                    'detected_by' => $detectedBy->id,
                ]);

                $createdCount++;
            }

            return $createdCount;
        });
    }

    /**
     * Reconcile paid weekly payments against current nominal.
     *
     * This is a fallback for cases where nominal has changed but adjustments
     * were not created during the original update flow.
     *
     * @param int $month
     * @param int $year
     * @param float $currentNominal
     * @param User $detectedBy
     * @return int
     */
    public function reconcileAdjustments(
        int $month,
        int $year,
        float $currentNominal,
        User $detectedBy
    ): int {
        return DB::transaction(function () use ($month, $year, $currentNominal, $detectedBy) {
            $payments = WeeklyPayment::where('month', $month)
                ->where('year', $year)
                ->where('status', 'paid')
                ->get();

            $createdCount = 0;

            foreach ($payments as $payment) {
                $difference = $currentNominal - $payment->amount;

                // Only reconcile if actual paid amount differs from current nominal.
                if ($difference == 0) {
                    continue;
                }

                $existingAdjustment = PaymentAdjustment::where('weekly_payment_id', $payment->id)->first();

                if ($existingAdjustment) {
                    // Keep existing pending adjustment or skip if already processed/cancelled.
                    continue;
                }

                PaymentAdjustment::create([
                    'weekly_payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'original_amount' => $payment->amount,
                    'current_nominal' => $currentNominal,
                    'adjustment_amount' => abs($difference),
                    'adjustment_type' => $difference > 0
                        ? 'shortage'
                        : 'overpayment',
                    'handling_method' => $difference > 0
                        ? 'invoice'
                        : 'refund',
                    'status' => 'pending',
                    'detected_by' => $detectedBy->id,
                ]);

                $createdCount++;
            }

            return $createdCount;
        });
    }

    /**
     * Buat payment adjustment record
     * 
     * @param WeeklyPayment $weeklyPayment
     * @param float $originalAmount Nominal saat pembayaran
     * @param float $currentNominal Nominal terbaru
     * @param User $detectedBy
     * @return PaymentAdjustment|null
     */
    public function createAdjustment(
        WeeklyPayment $weeklyPayment,
        float $originalAmount,
        float $currentNominal,
        User $detectedBy
    ): ?PaymentAdjustment {
        $difference = $currentNominal - $originalAmount;
        $adjustmentAmount = abs($difference);

        // Jika tidak ada selisih, return null
        if ($adjustmentAmount == 0) {
            return null;
        }

        $adjustmentType = $difference > 0 ? 'shortage' : 'overpayment';
        $handlingMethod = $adjustmentType === 'shortage' ? 'unpaid' : 'credit_balance';

        return PaymentAdjustment::create([
            'weekly_payment_id' => $weeklyPayment->id,
            'student_id' => $weeklyPayment->student_id,
            'original_amount' => $originalAmount,
            'current_nominal' => $currentNominal,
            'adjustment_amount' => $adjustmentAmount,
            'adjustment_type' => $adjustmentType,
            'status' => 'pending',
            'handling_method' => $handlingMethod,
            'detected_by' => $detectedBy->id,
        ]);
    }

    /**
     * Process shortage adjustment dengan method "Invoice Terpisah"
     * 
     * Flow:
     * 1. Buat transaction income untuk shortage
     * 2. Link ke adjustment
     * 3. Mark adjustment as processed
     * 
     * @param PaymentAdjustment $adjustment
     * @param User $processedBy
     * @param string|null $notes
     * @return Transaction
     */
    public function processShortageAsInvoice(
        PaymentAdjustment $adjustment,
        User $processedBy,
        ?string $notes = null
    ): Transaction {
        if (!$adjustment->isShortage()) {
            throw new \InvalidArgumentException('Adjustment harus bertipe shortage');
        }

        return DB::transaction(function () use ($adjustment, $processedBy, $notes) {
            // Buat transaction income
            $transaction = Transaction::create([
                'student_id' => $adjustment->student_id,
                'type' => 'income',
                'amount' => abs((float) $adjustment->adjustment_amount),
                'description' => "Invoice penyesuaian kas: Minggu {$adjustment->weeklyPayment->week_number} Bulan {$adjustment->weeklyPayment->month}/{$adjustment->weeklyPayment->year}",
                'date' => now()->toDateString(),
                'created_by' => $processedBy->id,
            ]);

            // Link ke adjustment
            $adjustment->update([
                'invoice_transaction_id' => $transaction->id,
            ]);

            // Mark as processed
            $adjustment->markAsProcessed($processedBy, $notes);

            return $transaction;
        });
    }

    /**
     * Process overpayment adjustment dengan method "Saldo Kredit"
     * 
     * Flow:
     * 1. Tambah credit ke StudentCreditBalance
     * 2. Optionally buat transaction untuk tracking
     * 3. Mark adjustment as processed
     * 
     * @param PaymentAdjustment $adjustment
     * @param User $processedBy
     * @param string|null $notes
     * @return StudentCreditBalance
     */
    public function processOverpaymentAsCredit(
        PaymentAdjustment $adjustment,
        User $processedBy,
        ?string $notes = null
    ): StudentCreditBalance {
        if (!$adjustment->isOverpayment()) {
            throw new \InvalidArgumentException('Adjustment harus bertipe overpayment');
        }

        return DB::transaction(function () use ($adjustment, $processedBy, $notes) {
            $creditAmount = abs((float) $adjustment->adjustment_amount);

            // Get atau create student credit balance
            $creditBalance = StudentCreditBalance::forStudent($adjustment->student);
            $creditBalance->addCredit($creditAmount);

            // Update adjustment
            $adjustment->update([
                'handling_method' => 'credit_balance',
            ]);

            // Mark as processed
            $adjustment->markAsProcessed($processedBy, $notes);

            return $creditBalance;
        });
    }

    /**
     * Process overpayment adjustment dengan method "Pengembalian Dana"
     * 
     * Flow:
     * 1. Buat transaction expense untuk refund
     * 2. Link ke adjustment
     * 3. Mark adjustment as processed
     * 
     * @param PaymentAdjustment $adjustment
     * @param User $processedBy
     * @param string|null $notes
     * @return Transaction
     */
    public function processOverpaymentAsRefund(
        PaymentAdjustment $adjustment,
        User $processedBy,
        ?string $notes = null
    ): Transaction {
        if (!$adjustment->isOverpayment()) {
            throw new \InvalidArgumentException('Adjustment harus bertipe overpayment');
        }

        return DB::transaction(function () use ($adjustment, $processedBy, $notes) {
            // Buat transaction expense
            $transaction = Transaction::create([
                'student_id' => $adjustment->student_id,
                'type' => 'expense',
                'amount' => abs((float) $adjustment->adjustment_amount),
                'description' => "Pengembalian dana penyesuaian kas: Minggu {$adjustment->weeklyPayment->week_number} Bulan {$adjustment->weeklyPayment->month}/{$adjustment->weeklyPayment->year}",
                'date' => now()->toDateString(),
                'created_by' => $processedBy->id,
            ]);

            // Link ke adjustment
            $adjustment->update([
                'refund_transaction_id' => $transaction->id,
                'handling_method' => 'refund',
            ]);

            // Mark as processed
            $adjustment->markAsProcessed($processedBy, $notes);

            return $transaction;
        });
    }

    /**
     * Process shortage adjustment dengan method "Tambah Tagihan" (unpaid)
     * 
     * Flow:
     * 1. Tambah amount ke weekly_payment yang existing (atau buat baru untuk minggu berikutnya)
     * 2. Mark adjustment as processed
     * 
     * Note: Ini adalah pendekatan simple - shortage disimpan dalam adjustment,
     * bukan diupdate weekly_payment untuk maintain immutability
     * 
     * @param PaymentAdjustment $adjustment
     * @param User $processedBy
     * @param string|null $notes
     * @return PaymentAdjustment
     */
    public function processShortageAsUnpaid(
        PaymentAdjustment $adjustment,
        User $processedBy,
        ?string $notes = null
    ): PaymentAdjustment {
        if (!$adjustment->isShortage()) {
            throw new \InvalidArgumentException('Adjustment harus bertipe shortage');
        }

        return DB::transaction(function () use ($adjustment, $processedBy, $notes) {
            // Update handling method
            $adjustment->update([
                'handling_method' => 'unpaid',
            ]);

            // Mark as processed
            $adjustment->markAsProcessed($processedBy, $notes);

            return $adjustment;
        });
    }

    /**
     * Cancel adjustment dengan reason
     * 
     * @param PaymentAdjustment $adjustment
     * @param string $reason
     * @return void
     */
    public function cancelAdjustment(PaymentAdjustment $adjustment, string $reason): void
    {
        DB::transaction(function () use ($adjustment, $reason) {
            // Jika sudah ada transaksi terkait, throw error
            if ($adjustment->getRelatedTransaction()) {
                throw new \InvalidArgumentException(
                    'Tidak bisa membatalkan adjustment yang sudah memiliki transaksi terkait'
                );
            }

            $adjustment->markAsCancelled($reason);
        });
    }

    /**
     * Get summary adjustment per student
     * 
     * @return Collection Summary dengan structure: student_id, student_name, total_shortage, total_overpayment, count
     */
    public function getSummaryPerStudent(): Collection
    {
        return PaymentAdjustment::pending()
            ->with('student')
            ->get()
            ->groupBy('student_id')
            ->map(function ($adjustments) {
                $shortage = $adjustments
                    ->where('adjustment_type', 'shortage')
                    ->sum('adjustment_amount');
                
                $overpayment = abs($adjustments
                    ->where('adjustment_type', 'overpayment')
                    ->sum('adjustment_amount'));

                return [
                    'student_id' => $adjustments->first()->student_id,
                    'student_name' => $adjustments->first()->student->name ?? 'N/A',
                    'total_shortage' => $shortage,
                    'total_overpayment' => $overpayment,
                    'count' => $adjustments->count(),
                    'adjustments' => $adjustments->all(),
                ];
            })
            ->values();
    }

    /**
     * Get adjustment yang butuh tindakan
     * 
     * @return Collection
     */
    public function getPendingAdjustments(): Collection
    {
        return PaymentAdjustment::pending()
            ->with('student', 'weeklyPayment')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get detail adjustment untuk display
     * 
     * @param PaymentAdjustment $adjustment
     * @return array
     */
    public function getAdjustmentDetail(PaymentAdjustment $adjustment): array
    {
        return [
            'id' => $adjustment->id,
            'student_name' => $adjustment->student->name,
            'student_email' => $adjustment->student->email,
            'week_number' => $adjustment->weeklyPayment->week_number,
            'month' => $adjustment->weeklyPayment->month,
            'year' => $adjustment->weeklyPayment->year,
            'original_amount' => $adjustment->original_amount,
            'current_nominal' => $adjustment->current_nominal,
            'adjustment_amount' => $adjustment->adjustment_amount,
            'adjustment_type' => $adjustment->adjustment_type_label,
            'status' => $adjustment->status_label,
            'handling_method' => $adjustment->handling_method_label,
            'detected_at' => $adjustment->created_at->format('d/m/Y H:i'),
            'processed_at' => $adjustment->processed_at?->format('d/m/Y H:i'),
            'detected_by' => $adjustment->detectedBy->name,
            'processed_by' => $adjustment->processedBy?->name,
            'notes' => $adjustment->notes,
        ];
    }

    /**
     * Generate report adjustment untuk period tertentu
     * 
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function generateReport(\DateTime $startDate, \DateTime $endDate): array
    {
        $adjustments = PaymentAdjustment::whereBetween('created_at', [$startDate, $endDate])
            ->with('student')
            ->get();

        return [
            'total_adjustments' => $adjustments->count(),
            'total_shortage' => $adjustments
                ->where('adjustment_type', 'shortage')
                ->sum('adjustment_amount'),
            'total_overpayment' => abs($adjustments
                ->where('adjustment_type', 'overpayment')
                ->sum('adjustment_amount')),
            'processed' => $adjustments->where('status', 'processed')->count(),
            'pending' => $adjustments->where('status', 'pending')->count(),
            'by_type' => [
                'shortage' => $adjustments->where('adjustment_type', 'shortage')->count(),
                'overpayment' => $adjustments->where('adjustment_type', 'overpayment')->count(),
            ],
            'by_handling_method' => $adjustments
                ->groupBy('handling_method')
                ->map(fn($group) => $group->count()),
        ];
    }
}

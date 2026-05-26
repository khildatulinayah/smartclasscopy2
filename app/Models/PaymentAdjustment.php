<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_payment_id',
        'student_id',
        'original_amount',
        'current_nominal',
        'adjustment_amount',
        'adjustment_type',
        'status',
        'handling_method',
        'invoice_transaction_id',
        'refund_transaction_id',
        'credit_transaction_id',
        'detected_by',
        'processed_by',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_nominal' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'adjustment_type_label',
        'handling_method_label',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Relasi ke weekly payment yang menjadi basis penyesuaian
     */
    public function weeklyPayment(): BelongsTo
    {
        return $this->belongsTo(WeeklyPayment::class);
    }

    /**
     * Relasi ke student (user)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relasi ke user yang mendeteksi adjustment
     */
    public function detectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'detected_by');
    }

    /**
     * Relasi ke user yang memproses adjustment
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Relasi ke transaction invoice (untuk shortage)
     */
    public function invoiceTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'invoice_transaction_id');
    }

    /**
     * Relasi ke transaction refund (untuk overpayment)
     */
    public function refundTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'refund_transaction_id');
    }

    /**
     * Relasi ke transaction credit (untuk overpayment yang disimpan)
     */
    public function creditTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'credit_transaction_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope: adjustment yang masih pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: adjustment yang sudah diproses
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Scope: adjustment yang dibatalkan
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope: adjustment tipe shortage (kurang bayar)
     */
    public function scopeShortage($query)
    {
        return $query->where('adjustment_type', 'shortage');
    }

    /**
     * Scope: adjustment tipe overpayment (kelebihan bayar)
     */
    public function scopeOverpayment($query)
    {
        return $query->where('adjustment_type', 'overpayment');
    }

    /**
     * Scope: filter by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope: filter by handling method
     */
    public function scopeByHandlingMethod($query, $method)
    {
        return $query->where('handling_method', $method);
    }

    /**
     * Scope: adjustment yang butuh transaksi terkait
     */
    public function scopeNeedsTransaction($query)
    {
        return $query->whereNull('invoice_transaction_id')
            ->whereNull('refund_transaction_id')
            ->whereNull('credit_transaction_id')
            ->pending();
    }

    // ========== ACCESSORS & MUTATORS ==========

    /**
     * Label status untuk display
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Diproses',
            'processed' => 'Sudah Diproses',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Label adjustment type untuk display
     */
    public function getAdjustmentTypeLabelAttribute(): string
    {
        return match ($this->adjustment_type) {
            'shortage' => 'Kurang Bayar',
            'overpayment' => 'Kelebihan Bayar',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Label handling method untuk display
     */
    public function getHandlingMethodLabelAttribute(): string
    {
        return match ($this->handling_method) {
            'unpaid' => 'Tambah Tagihan',
            'invoice' => 'Invoice Terpisah',
            'credit_balance' => 'Saldo Kredit',
            'refund' => 'Pengembalian Dana',
            default => 'Tidak Diketahui',
        };
    }

    // ========== BUSINESS LOGIC METHODS ==========

    /**
     * Tandai adjustment sebagai sudah diproses
     */
    public function markAsProcessed(User $processedBy, ?string $notes = null): void
    {
        $this->update([
            'status' => 'processed',
            'processed_by' => $processedBy->id,
            'processed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Tandai adjustment sebagai dibatalkan
     */
    public function markAsCancelled(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => ($this->notes ? $this->notes . "\n" : '') . "Dibatalkan: {$reason}",
        ]);
    }

    /**
     * Check apakah adjustment bisa diproses
     */
    public function canBeProcessed(): bool
    {
        return $this->status === 'pending' &&
            $this->weeklyPayment()->exists() &&
            !is_null($this->detected_by);
    }

    /**
     * Get transaksi terkait berdasarkan handling method
     */
    public function getRelatedTransaction(): ?Transaction
    {
        return match ($this->handling_method) {
            'invoice' => $this->invoiceTransaction,
            'refund' => $this->refundTransaction,
            'credit_balance' => $this->creditTransaction,
            default => null,
        };
    }

    /**
     * Check apakah adjustment adalah shortage (kurang bayar)
     */
    public function isShortage(): bool
    {
        return $this->adjustment_type === 'shortage';
    }

    /**
     * Check apakah adjustment adalah overpayment (kelebihan bayar)
     */
    public function isOverpayment(): bool
    {
        return $this->adjustment_type === 'overpayment';
    }

    /**
     * Get absolute amount untuk display
     */
    public function getAbsoluteAmountAttribute(): float
    {
        return abs((float) $this->adjustment_amount);
    }

    /**
     * Get formatted rupiah untuk display
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format(abs((float) $this->adjustment_amount), 0, ',', '.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDifference extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_payment_id',
        'student_id',
        'old_nominal',
        'new_nominal',
        'difference',
        'status',
        'action_type',
        'settlement_transaction_id',
        'settlement_date',
        'notes',
        'created_by',
        'processed_by',
    ];

    protected $casts = [
        'old_nominal' => 'decimal:2',
        'new_nominal' => 'decimal:2',
        'difference' => 'decimal:2',
        'settlement_date' => 'date',
    ];

    // ===== RELASI =====
    
    public function weeklyPayment()
    {
        return $this->belongsTo(WeeklyPayment::class, 'weekly_payment_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function settlementTransaction()
    {
        return $this->belongsTo(Transaction::class, 'settlement_transaction_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSettled($query)
    {
        return $query->where('status', 'settled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeNeedsSettlement($query)
    {
        return $query->where('action_type', 'settlement')->where('status', 'pending');
    }

    public function scopeNeedsRefund($query)
    {
        return $query->where('action_type', 'refund')->where('status', 'pending');
    }

    // ===== HELPER METHODS =====

    /**
     * Tandai sebagai sudah diselesaikan (settlement atau refund)
     */
    public function markAsSettled($transactionId = null, $processedBy = null)
    {
        $this->update([
            'status' => 'settled',
            'settlement_transaction_id' => $transactionId,
            'settlement_date' => now()->toDateString(),
            'processed_by' => $processedBy ?? auth()->id(),
        ]);

        return $this;
    }

    /**
     * Tandai sebagai sudah dikembalikan
     */
    public function markAsRefunded($transactionId = null, $processedBy = null)
    {
        $this->update([
            'status' => 'refunded',
            'settlement_transaction_id' => $transactionId,
            'settlement_date' => now()->toDateString(),
            'processed_by' => $processedBy ?? auth()->id(),
        ]);

        return $this;
    }

    /**
     * Format untuk display
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Menunggu</span>',
            'settled' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Terselesaikan</span>',
            'refunded' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Dikembalikan</span>',
            default => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">Tidak Diketahui</span>',
        };
    }

    public function getActionTypeLabelAttribute()
    {
        return match($this->action_type) {
            'settlement' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Pelunasan</span>',
            'refund' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">Pengembalian</span>',
            default => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">-</span>',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'amount',
        'description',
        'date',
        'created_by',
        'receipt_path',
        'weekly_payment_id',
        'payment_adjustment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function weeklyPayment()
    {
        return $this->belongsTo(WeeklyPayment::class, 'weekly_payment_id');
    }

    public function paymentAdjustment()
    {
        return $this->belongsTo(PaymentAdjustment::class, 'payment_adjustment_id');
    }


    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk filter transaksi
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }
}


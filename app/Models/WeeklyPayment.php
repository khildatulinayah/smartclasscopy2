<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'week_number',
        'month',
        'year',
        'amount',
        'status',
        'payment_date',
        'transaction_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relasi ke siswa
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi ke transaksi kas (jika sudah bayar)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    // Relasi ke user yang input
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope: yang sudah bayar
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Scope: yang belum bayar
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    // Scope: bulan tertentu
    public function scopeMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    // Helper: generate tagihan mingguan untuk siswa
    public static function generateWeeklyBills($studentId, $month, $year, $amountPerWeek = 5000)
    {
        $bills = [];
        $weeksInMonth = self::getWeeksInMonth($month, $year);
        
        for ($week = 1; $week <= $weeksInMonth; $week++) {
            // Cek apakah sudah ada
            $existing = self::where('student_id', $studentId)
                           ->where('week_number', $week)
                           ->where('month', $month)
                           ->where('year', $year)
                           ->first();
            
            if (!$existing) {
                $bills[] = [
                    'student_id' => $studentId,
                    'week_number' => $week,
                    'month' => $month,
                    'year' => $year,
                    'amount' => $amountPerWeek,
                    'status' => 'unpaid',
                    'payment_date' => null,
                    'transaction_id' => null,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        if (!empty($bills)) {
            self::insert($bills);
        }
        
        return count($bills);
    }

    // Helper: hitung jumlah minggu dalam bulan
    private static function getWeeksInMonth($month, $year)
    {
        // Sederhana: anggap 4 minggu per bulan
        return 4;
    }

    // Helper: hitung total tunggakan siswa
    public static function calculateArrears($studentId)
    {
        return self::where('student_id', $studentId)
                  ->where('status', 'unpaid')
                  ->sum('amount');
    }

    // Helper: dapatkan status pembayaran bulanan siswa
    public static function getMonthlyStatus($studentId, $month, $year)
    {
        $payments = self::where('student_id', $studentId)
                      ->where('month', $month)
                      ->where('year', $year)
                      ->orderBy('week_number')
                      ->get();
        
        $totalBills = $payments->count();
        $paidBills = $payments->where('status', 'paid')->count();
        $unpaidBills = $payments->where('status', 'unpaid')->count();
        $totalAmount = $payments->sum('amount');
        $paidAmount = $payments->where('status', 'paid')->sum('amount');
        $unpaidAmount = $payments->where('status', 'unpaid')->sum('amount');
        
        return [
            'total_bills' => $totalBills,
            'paid_bills' => $paidBills,
            'unpaid_bills' => $unpaidBills,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'unpaid_amount' => $unpaidAmount,
            'details' => $payments,
        ];
    }
}
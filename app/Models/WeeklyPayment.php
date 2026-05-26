<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\KasSetting;
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

    /**
     * Append computed attributes to model JSON
     */
    protected $appends = [
        'paid_with_old_nominal',
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

    // Relasi ke payment adjustment (jika ada penyesuaian)
    public function adjustment()
    {
        return $this->hasOne(PaymentAdjustment::class, 'weekly_payment_id');
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
    public static function generateWeeklyBills($studentId, $month, $year, $amountPerWeek = null)
    {
        // Ambil nominal per bulan/tahun dari kas_settings, atau gunakan parameter jika diberikan
        if ($amountPerWeek === null) {
            $amountPerWeek = KasSetting::getNominal((int) $month, (int) $year) ?? 0;
        }
        
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

    /**
     * Sync monthly bills - Buat/update tagihan untuk semua siswa aktif di bulan tertentu
     * Ini idempotent: akan membuat tagihan untuk siswa baru, tapi tidak mengubah yang sudah ada
     */
    public static function syncMonthlyBills($month, $year, $amountPerWeek = null)
    {
        if ($amountPerWeek === null) {
            $amountPerWeek = KasSetting::getNominal((int) $month, (int) $year) ?? 0;
        }
        $students = User::where('role', 'siswa')->where('is_active', true)->get();
        $generatedCount = 0;
        
        foreach ($students as $student) {
            // Cek berapa tagihan yang sudah ada untuk bulan ini
            $existingCount = self::where('student_id', $student->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->count();
            
            $weeksInMonth = self::getWeeksInMonth($month, $year);
            
            // Jika belum ada tagihan sama sekali, buat untuk semua minggu
            if ($existingCount === 0) {
                for ($week = 1; $week <= $weeksInMonth; $week++) {
                    self::create([
                        'student_id' => $student->id,
                        'week_number' => $week,
                        'month' => $month,
                        'year' => $year,
                        'amount' => $amountPerWeek,
                        'status' => 'unpaid',
                        'payment_date' => null,
                        'transaction_id' => null,
                        'created_by' => auth()->check() ? auth()->id() : 1,
                    ]);
                    $generatedCount++;
                }
            } else if ($existingCount < $weeksInMonth) {
                // Jika ada minggu yang belum ada, buat yang kurang
                for ($week = 1; $week <= $weeksInMonth; $week++) {
                    $existing = self::where('student_id', $student->id)
                                    ->where('week_number', $week)
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->first();
                    
                    if (!$existing) {
                        self::create([
                            'student_id' => $student->id,
                            'week_number' => $week,
                            'month' => $month,
                            'year' => $year,
                            'amount' => $amountPerWeek,
                            'status' => 'unpaid',
                            'payment_date' => null,
                            'transaction_id' => null,
                            'created_by' => auth()->check() ? auth()->id() : 1,
                        ]);
                        $generatedCount++;
                    }
                }
            }
        }
        
        return $generatedCount;
    }

    /**
     * Hitung jumlah minggu dalam bulan berdasarkan hari Rabu
     * Hari Rabu adalah hari pembayaran kas
     */
    public static function getWeeksInMonth($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $wednesdayCount = 0;
        $current = $startDate->copy();
        
        // Iterate through all days in the month and count Wednesdays
        while ($current->lte($endDate)) {
            if ($current->dayOfWeek === 3) { // 3 = Wednesday (Carbon::WEDNESDAY)
                $wednesdayCount++;
            }
            $current->addDay();
        }
        
        return $wednesdayCount > 0 ? $wednesdayCount : 4; // Minimal 4 minggu jika tidak ada Rabu
    }

    /**
     * Dapatkan daftar tanggal Rabu dalam bulan tertentu
     * Berguna untuk menampilkan di UI atau keperluan lain
     */
    public static function getWednesdayDatesInMonth($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $wednesdays = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if ($current->dayOfWeek === 3) { // 3 = Wednesday
                $wednesdays[] = $current->copy();
            }
            $current->addDay();
        }
        
        return $wednesdays;
    }

    /**
     * Get weekly payment amount from settings
     */
    public static function getWeeklyPaymentAmount($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        return KasSetting::getNominal((int) $month, (int) $year) ?? 0;
    }

    /**
     * Accessor: apakah pembayaran sudah dilakukan namun dengan nominal lama
     * (mis. siswa bayar ketika nominal masih Rp 6000, sementara sekarang nominalnya Rp 7000)
     */
    public function getPaidWithOldNominalAttribute()
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $currentNominal = KasSetting::getNominal((int) $this->month, (int) $this->year) ?? 0;

        return (float) $this->amount < (float) $currentNominal;
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

    // ========== PAYMENT ADJUSTMENT HELPER METHODS ==========

    /**
     * Check apakah weekly payment ini memiliki adjustment
     */
    public function hasAdjustment(): bool
    {
        return $this->adjustment()->exists();
    }

    /**
     * Check apakah ada adjustment yang masih pending
     */
    public function hasPendingAdjustment(): bool
    {
        return $this->adjustment()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Get adjustment status jika ada
     */
    public function getAdjustmentStatus(): ?string
    {
        return $this->adjustment?->status;
    }

    /**
     * Get adjustment type label jika ada
     */
    public function getAdjustmentTypeLabel(): ?string
    {
        return $this->adjustment?->adjustment_type_label;
    }

    /**
     * Get adjustment amount jika ada
     */
    public function getAdjustmentAmount(): ?float
    {
        return $this->adjustment?->adjustment_amount;
    }

    /**
     * Get effective amount (yang seharusnya dibayar saat ini)
     * Ini adalah current nominal dari KasSetting, bukan amount yang di record
     */
    public function getEffectiveAmount(): float
    {
        $currentNominal = KasSetting::getNominal((int) $this->month, (int) $this->year) ?? 0;
        return (float) $currentNominal;
    }

    /**
     * Get difference amount (difference between recorded amount and current nominal)
     */
    public function getDifferenceAmount(): float
    {
        return $this->getEffectiveAmount() - (float) $this->amount;
    }
}

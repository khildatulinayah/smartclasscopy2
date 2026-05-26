<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCreditBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_credit',
        'last_updated_at',
    ];

    protected $casts = [
        'total_credit' => 'decimal:2',
        'last_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_credit',
        'has_credit',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Relasi ke student (user)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope: siswa dengan kredit lebih dari 0
     */
    public function scopeHasCredit($query)
    {
        return $query->where('total_credit', '>', 0);
    }

    /**
     * Scope: order by total kredit descending
     */
    public function scopeOrderByCredit($query, $direction = 'desc')
    {
        return $query->orderBy('total_credit', $direction);
    }

    // ========== ACCESSORS ==========

    /**
     * Format kredit dalam rupiah
     */
    public function getFormattedCreditAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_credit, 0, ',', '.');
    }

    /**
     * Check apakah siswa punya kredit
     */
    public function getHasCreditAttribute(): bool
    {
        return (float) $this->total_credit > 0;
    }

    // ========== BUSINESS LOGIC METHODS ==========

    /**
     * Tambah kredit ke saldo
     * 
     * @param float|int $amount Jumlah yang ditambahkan
     * @return void
     */
    public function addCredit($amount): void
    {
        if ($amount > 0) {
            $this->increment('total_credit', $amount);
            $this->update(['last_updated_at' => now()]);
        }
    }

    /**
     * Gunakan kredit dari saldo
     * 
     * @param float|int $amount Jumlah yang digunakan
     * @return bool true jika berhasil, false jika kredit tidak cukup
     */
    public function useCredit($amount): bool
    {
        if ($amount > 0 && (float) $this->total_credit >= $amount) {
            $this->decrement('total_credit', $amount);
            $this->update(['last_updated_at' => now()]);
            return true;
        }
        return false;
    }

    /**
     * Get atau create kredit balance untuk student
     * 
     * @param User $student
     * @return self
     */
    public static function forStudent(User $student): self
    {
        return static::firstOrCreate(
            ['student_id' => $student->id],
            ['total_credit' => 0, 'last_updated_at' => null]
        );
    }

    /**
     * Reset kredit menjadi 0 (untuk keperluan tertentu)
     * 
     * @param string|null $reason Alasan reset
     * @return void
     */
    public function reset(?string $reason = null): void
    {
        $this->update([
            'total_credit' => 0,
            'last_updated_at' => now(),
        ]);
    }

    /**
     * Get summary kredit untuk reporting
     */
    public static function getSummary()
    {
        return static::select(
            'student_id',
            'total_credit'
        )
            ->with('student:id,name,email')
            ->hasCredit()
            ->orderByCredit()
            ->get();
    }

    /**
     * Get total kredit seluruh siswa
     */
    public static function getTotalCredit()
    {
        return static::sum('total_credit');
    }
}

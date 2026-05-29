<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'note',
        'created_by',
        'is_national_holiday',
    ];

    protected $casts = [
        'date' => 'date',
        'is_national_holiday' => 'boolean',
    ];

    // ============= RELATIONSHIPS =============

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ============= SCOPES =============

    /**
     * Scope: Filter hari libur untuk tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        $dateString = is_string($date) ? $date : $date->format('Y-m-d');
        return $query->where('date', $dateString)->first();
    }

    /**
     * Scope: Filter hari libur untuk bulan tertentu
     */
    public function scopeForMonth($query, $month, $year = null)
    {
        $year = $year ?? now()->year;
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    /**
     * Scope: Filter hari libur untuk tahun tertentu
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Scope: Filter hari libur yang sudah lewat
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    /**
     * Scope: Filter hari libur yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope: Filter hari libur berdasarkan range tanggal
     */
    public function scopeBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter hari libur berdasarkan pencarian
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('note', 'like', '%' . $search . '%');
    }

    // ============= ACCESSORS & MUTATORS =============

    /**
     * Accessor: Format tanggal dalam bahasa Indonesia
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->locale('id')->translatedFormat('l, d F Y');
    }

    /**
     * Accessor: Nama hari dalam bahasa Indonesia
     */
    public function getDayNameAttribute()
    {
        return $this->date->locale('id')->translatedFormat('l');
    }

    /**
     * Accessor: Format tanggal singkat (d M Y)
     */
    public function getShortDateAttribute()
    {
        return $this->date->locale('id')->translatedFormat('d M Y');
    }

    // ============= HELPER METHODS =============

    /**
     * Check apakah tanggal tertentu adalah hari libur
     */
    public static function isHoliday($date)
    {
        $dateString = is_string($date) ? $date : $date->format('Y-m-d');
        return self::where('date', $dateString)->exists();
    }

    /**
     * Get daftar hari libur dalam bulan tertentu
     */
    public static function getHolidaysInMonth($month, $year = null)
    {
        $year = $year ?? now()->year;
        return self::forMonth($month, $year)->pluck('date')->map(function($date) {
            return $date->format('Y-m-d');
        })->toArray();
    }

    /**
     * Get daftar hari libur dalam tahun tertentu
     */
    public static function getHolidaysInYear($year)
    {
        return self::forYear($year)->pluck('date')->map(function($date) {
            return $date->format('Y-m-d');
        })->toArray();
    }

    /**
     * Get static list of Indonesian national public holidays for a given year.
     */
    public static function getIndonesianNationalHolidays($year = null)
    {
        $year = $year ?? now()->year;

        return collect([
            [
                'date' => sprintf('%04d-01-01', $year),
                'note' => 'Tahun Baru Masehi'
            ],
            [
                'date' => sprintf('%04d-05-01', $year),
                'note' => 'Hari Buruh Internasional'
            ],
            [
                'date' => sprintf('%04d-06-01', $year),
                'note' => 'Hari Lahir Pancasila'
            ],
            [
                'date' => sprintf('%04d-08-17', $year),
                'note' => 'Hari Kemerdekaan Republik Indonesia'
            ],
            [
                'date' => sprintf('%04d-12-25', $year),
                'note' => 'Hari Natal'
            ],
        ]);
    }

    /**
     * Hitung total hari libur dalam bulan tertentu
     */
    public static function countHolidaysInMonth($month, $year = null)
    {
        $year = $year ?? now()->year;
        return self::forMonth($month, $year)->count();
    }

    /**
     * Check apakah tanggal tertentu adalah hari kerja
     */
    public static function isWorkingDay($date)
    {
        $carbonDate = is_string($date) ? \Carbon\Carbon::parse($date) : $date;
        
        // Cek apakah weekend
        if ($carbonDate->isWeekend()) {
            return false;
        }
        
        // Cek apakah hari libur
        return !self::isHoliday($date);
    }
}



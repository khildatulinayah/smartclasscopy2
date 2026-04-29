<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'status',
        'attendance_time',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'attendance_time' => 'datetime:H:i:s',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'belum_absen' => 'Belum Absen',
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpha' => 'Alpha'
        ];
        
        return $statusTexts[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'belum_absen' => 'gray',
            'hadir' => 'green',
            'sakit' => 'yellow',
            'izin' => 'blue',
            'alpha' => 'red'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    public function scopeForToday($query)
    {
        return $query->where('date', now()->format('Y-m-d'));
    }

    public function scopeForMonth($query, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        return $query->whereMonth('date', $month)
                    ->whereYear('date', $year);
    }
}
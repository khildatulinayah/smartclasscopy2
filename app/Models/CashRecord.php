<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRecord extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'week_start', 'status', 'amount'];

    protected $casts = [
        'week_start' => 'date',
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
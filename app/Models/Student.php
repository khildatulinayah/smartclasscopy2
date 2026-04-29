<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nis',
        'class',
    ];

    /**
     * The user this student profile belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cash records associated with this student.
     */
    public function cashRecords()
    {
        return $this->hasMany(CashRecord::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'nominal',
    ];

    public static function getNominal(int $month, int $year): ?int
    {
        $nominal = static::where('month', $month)
            ->where('year', $year)
            ->value('nominal');

        return $nominal !== null ? (int) $nominal : null;
    }
}

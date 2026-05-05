<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        return $value;
    }

    public static function getKasNominal($default = 5000)
    {
        $kasNominal = static::where('key', 'kas_nominal')->value('value');

        if ($kasNominal !== null) {
            return (int) $kasNominal;
        }

        // Backward compatibility for old key.
        $legacyNominal = static::where('key', 'weekly_payment_amount')->value('value');

        return $legacyNominal !== null ? (int) $legacyNominal : (int) $default;
    }
}


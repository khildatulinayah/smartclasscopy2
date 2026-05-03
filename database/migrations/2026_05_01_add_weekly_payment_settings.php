<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default settings if not exists
        Setting::updateOrCreate(
            ['key' => 'weekly_payment_amount'],
            [
                'value' => '5000',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'payment_day_of_week'],
            [
                'value' => '3', // 3 = Wednesday (Carbon::WEDNESDAY)
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is optional - you can leave settings as is
        // or delete them
        // Setting::where('key', 'weekly_payment_amount')->delete();
        // Setting::where('key', 'payment_day_of_week')->delete();
    }
};

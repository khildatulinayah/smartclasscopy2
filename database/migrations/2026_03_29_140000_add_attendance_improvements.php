<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new status to enum
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status_new', ['belum_absen', 'hadir', 'sakit', 'izin', 'alpha'])->default('belum_absen')->after('status');
        });

        // Copy data from old column to new column
        DB::statement("
            UPDATE attendances 
            SET status_new = CASE 
                WHEN status = 'hadir' THEN 'hadir'
                WHEN status = 'sakit' THEN 'sakit'
                WHEN status = 'izin' THEN 'izin'
                WHEN status = 'alpha' THEN 'alpha'
                ELSE 'belum_absen'
            END
        ");

        // Drop old column and rename new column
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });

        // Add attendance_time column
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('attendance_time')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop attendance_time column
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('attendance_time');
        });

        // Restore old enum
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status_old', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir')->after('status');
        });

        // Copy data back
        DB::statement("
            UPDATE attendances 
            SET status_old = CASE 
                WHEN status = 'hadir' THEN 'hadir'
                WHEN status = 'sakit' THEN 'sakit'
                WHEN status = 'izin' THEN 'izin'
                WHEN status = 'alpha' THEN 'alpha'
                WHEN status = 'belum_absen' THEN 'hadir'
                ELSE 'hadir'
            END
        ");

        // Drop new column and rename old column
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};

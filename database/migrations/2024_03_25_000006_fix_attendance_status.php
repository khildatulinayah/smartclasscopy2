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
        // Create a new column with the correct enum values
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status_new', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir')->after('status');
        });

        // Copy data from old column to new column, converting 'tidak' to 'alpha'
        DB::statement("
            UPDATE attendances 
            SET status_new = CASE 
                WHEN status = 'tidak' THEN 'alpha'
                WHEN status = 'hadir' THEN 'hadir'
                WHEN status = 'sakit' THEN 'sakit'
                WHEN status = 'izin' THEN 'izin'
                ELSE 'hadir'
            END
        ");

        // Drop old column and rename new column
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create old column structure
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status_old', ['hadir', 'tidak', 'sakit', 'izin'])->default('tidak')->after('status');
        });

        // Copy data back, converting 'alpha' to 'tidak'
        DB::statement("
            UPDATE attendances 
            SET status_old = CASE 
                WHEN status = 'alpha' THEN 'tidak'
                WHEN status = 'hadir' THEN 'hadir'
                WHEN status = 'sakit' THEN 'sakit'
                WHEN status = 'izin' THEN 'izin'
                ELSE 'tidak'
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

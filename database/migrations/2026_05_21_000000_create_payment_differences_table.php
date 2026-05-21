<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk tracking selisih nominal ketika terjadi perubahan nominal kas
     * Ketika nominal berubah setelah siswa melakukan pembayaran, sistem mencatat:
     * - Nominal yang dibayarkan (old nominal)
     * - Nominal baru dari settings
     * - Selisih dan status penyelesaiannya
     */
    public function up()
    {
        Schema::create('payment_differences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_payment_id')->constrained('weekly_payments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('old_nominal', 10, 2); // Nominal yang dibayarkan
            $table->decimal('new_nominal', 10, 2); // Nominal baru
            $table->decimal('difference', 10, 2); // Selisih (positif = kekurangan, negatif = kelebihan)
            
            // Status: pending, settled (untuk kelebihan), refunded (untuk kekurangan)
            $table->enum('status', ['pending', 'settled', 'refunded'])->default('pending');
            $table->enum('action_type', ['settlement', 'refund']); // settlement = bayar lebih, refund = kembalikan
            
            $table->foreignId('settlement_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->date('settlement_date')->nullable();
            
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index untuk query yang sering
            $table->index(['student_id', 'status']);
            $table->index(['weekly_payment_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_differences');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('week_number'); // 1, 2, 3, 4 (minggu ke berapa)
$table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('amount', 10, 2); // Rp 5.000 per minggu
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->date('payment_date')->nullable(); // Tanggal bayar
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Unique: satu siswa hanya punya satu record per minggu dalam bulan yang sama
            $table->unique(['student_id', 'week_number', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_payments');
    }
};

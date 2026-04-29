<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('week_number'); // 1, 2, 3, 4 (minggu ke berapa dalam bulan)
            $table->string('month'); // januari, februari, dll
            $table->integer('year');
            $table->date('payment_date'); // Tanggal pembayaran (hari Rabu)
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['student_id', 'week_number', 'month', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_payments');
    }
};

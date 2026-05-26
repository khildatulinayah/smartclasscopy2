<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_adjustments', function (Blueprint $table) {
            $table->id();

            // Foreign Keys & Identitas
            $table->foreignId('weekly_payment_id')
                ->constrained('weekly_payments')
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Data Nominal
            $table->decimal('original_amount', 15, 2)
                ->comment('Nominal saat pembayaran dilakukan');
            $table->decimal('current_nominal', 15, 2)
                ->comment('Nominal terbaru dari KasSetting');
            $table->decimal('adjustment_amount', 15, 2)
                ->comment('Selisih pembayaran (positif=shortage, negatif=overpayment)');

            // Type & Status
            $table->enum('adjustment_type', ['shortage', 'overpayment'])
                ->comment('shortage=kurang bayar, overpayment=kelebihan bayar');
            $table->enum('status', ['pending', 'processed', 'cancelled'])
                ->default('pending')
                ->comment('pending=menunggu, processed=selesai, cancelled=dibatalkan');
            $table->enum('handling_method', ['unpaid', 'invoice', 'credit_balance', 'refund'])
                ->default('unpaid')
                ->comment('Cara penanganan: unpaid=tagihan biasa, invoice=invoice terpisah, credit_balance=kredit saldo, refund=pengembalian');

            // Transaksi Terkait (nullable karena belum ada saat created)
            $table->foreignId('invoice_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete()
                ->comment('FK ke transaction jika dibuat invoice');
            $table->foreignId('refund_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete()
                ->comment('FK ke transaction jika ada pengembalian dana');
            $table->foreignId('credit_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete()
                ->comment('FK ke transaction jika disimpan sebagai kredit');

            // Audit Trail
            $table->foreignId('detected_by')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('User yang mendeteksi penyesuaian');
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User yang memproses penyesuaian');
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->timestamp('processed_at')->nullable();

            // Indexes
            $table->unique('weekly_payment_id');
            $table->index(['student_id', 'status']);
            $table->index('created_at');
            $table->index('adjustment_type');
            $table->index('handling_method');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_adjustments');
    }
};

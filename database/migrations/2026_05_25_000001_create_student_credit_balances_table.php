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
        Schema::create('student_credit_balances', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->foreignId('student_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Credit Balance
            $table->decimal('total_credit', 15, 2)
                ->default(0)
                ->comment('Total kredit saldo siswa dari overpayment');

            // Audit
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('total_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_credit_balances');
    }
};

<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\WeeklyPayment;
use App\Models\Transaction;
use App\Models\User;

#[Signature('app:test-payment-process')]
#[Description('Test payment process manually')]
class TestPaymentProcess extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing manual payment process...');
        
        // Ambil payment pertama yang unpaid
        $payment = WeeklyPayment::where('status', 'unpaid')->first();
        if (!$payment) {
            $this->error('Tidak ada unpaid payment');
            return 1;
        }
        
        $this->info("Payment ID: {$payment->id}");
        $this->info("Student: {$payment->student->name}");
        $this->info("Amount: {$payment->amount}");
        $this->info("Payment Date: {$payment->payment_date}");
        $this->info("Status: {$payment->status}");
        
        // Buat transaction manual
        $transaction = Transaction::create([
            'student_id' => $payment->student_id,
            'type' => 'income',
            'amount' => $payment->amount,
            'description' => 'Test pembayaran manual',
            'date' => now()->format('Y-m-d'),
            'created_by' => 1,
        ]);
        
        $this->info("\nTransaction created:");
        $this->info("Transaction ID: {$transaction->id}");
        $this->info("Amount: {$transaction->amount}");
        
        // Update payment
        $payment->update([
            'status' => 'paid',
            'transaction_id' => $transaction->id,
        ]);
        
        $this->info("\nPayment updated:");
        $this->info("New Status: {$payment->status}");
        $this->info("Transaction ID: {$payment->transaction_id}");
        
        // Verify
        $updatedPayment = WeeklyPayment::find($payment->id);
        $this->info("\nVerification:");
        $this->info("Status: {$updatedPayment->status}");
        $this->info("Transaction ID: {$updatedPayment->transaction_id}");
        
        $this->info("\nTest completed successfully!');
        
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\WeeklyPayment;
use App\Models\User;

#[Signature('app:test-wednesday-payments')]
#[Description('Test Wednesday payment generation')]
class TestWednesdayPayments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Wednesday payment generation for April 2026...');
        
        // Test helper function getWednesdays
        $wednesdays = WeeklyPayment::getWednesdays(4, 2026);
        $this->info("\nTanggal Rabu di April 2026:");
        foreach ($wednesdays as $index => $wednesday) {
            $this->line("Minggu " . ($index + 1) . ": " . $wednesday->format('Y-m-d l'));
        }
        
        // Get first student for testing
        $student = User::where('role', 'siswa')->first();
        if (!$student) {
            $this->error('Tidak ada student dengan role siswa');
            return 1;
        }
        
        $this->info("\nStudent: " . $student->name . " (ID: " . $student->id . ")");
        
        // Test generateWednesdayBills
        $this->info("\nGenerating bills for single student...");
        $generatedCount = WeeklyPayment::generateWednesdayBills($student->id, 4, 2026, 5000);
        $this->info("Generated $generatedCount bills");
        
        // Test generate for all students
        $this->info("\nGenerating bills for ALL students...");
        $allStudents = User::where('role', 'siswa')->get();
        $totalGenerated = 0;
        
        foreach ($allStudents as $s) {
            $count = WeeklyPayment::generateWednesdayBills($s->id, 4, 2026, 5000);
            $totalGenerated += $count;
            $this->line("Student {$s->name}: {$count} bills");
        }
        
        $this->info("Total generated for all students: $totalGenerated bills");
        
        // Check results
        $payments = WeeklyPayment::where('student_id', $student->id)
                                ->where('month', 4)
                                ->where('year', 2026)
                                ->orderBy('payment_date')
                                ->get();
        
        $this->info("\nHasil pembayaran:");
        foreach ($payments as $payment) {
            $this->line("Week {$payment->week_number}: {$payment->payment_date} - {$payment->status} - Rp " . number_format($payment->amount, 0, ',', '.'));
        }
        
        $this->info("\nTotal: " . $payments->count() . " payments");
        
        return 0;
    }
}

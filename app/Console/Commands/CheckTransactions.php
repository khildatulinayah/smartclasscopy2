<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Transaction;

#[Signature('app:check-transactions')]
#[Description('Check existing transactions')]
class CheckTransactions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking transactions...');
        
        $totalTransactions = Transaction::count();
        $incomeTransactions = Transaction::where('type', 'income')->count();
        $recentTransactions = Transaction::with(['student', 'creator'])
                                         ->orderBy('created_at', 'desc')
                                         ->take(5)
                                         ->get();
        
        $this->info("Total transactions: {$totalTransactions}");
        $this->info("Income transactions: {$incomeTransactions}");
        $this->info("Recent transactions:");
        
        foreach ($recentTransactions as $transaction) {
            $studentName = $transaction->student ? $transaction->student->name : 'No student';
            $creatorName = $transaction->creator ? $transaction->creator->name : 'No creator';
            $this->line("- {$transaction->description} ({$transaction->amount}) - Student: {$studentName}, Creator: {$creatorName}, Created: {$transaction->created_at}");
        }
        
        // Test API response format
        $this->info("\nTesting API response format...");
        $controller = new \App\Http\Controllers\BendaharaController();
        $apiResponse = $controller->getTransactions();
        $data = $apiResponse->getData();
        $this->info("API data type: " . gettype($data));
        if (is_object($data)) {
            $this->info("API returns " . count($data->transactions) . " transactions");
            $this->info("API summary - Income: {$data->summary->totalIncome}, Expense: {$data->summary->totalExpense}");
        } else {
            $this->info("API returns " . count($data['transactions']) . " transactions");
            $this->info("API summary - Income: {$data['summary']['totalIncome']}, Expense: {$data['summary']['totalExpense']}");
        }
        
        return 0;
    }
}

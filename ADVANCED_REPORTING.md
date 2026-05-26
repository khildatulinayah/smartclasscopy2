# Advanced Reporting & Queries - Payment Adjustment System

## 1. Reconciliation Queries

### Summary per Month

```php
use App\Models\PaymentAdjustment;

$month = 5;
$year = 2026;

$monthSummary = PaymentAdjustment::whereBetween('created_at', [
    now()->setDate($year, $month, 1),
    now()->setDate($year, $month, 30)->endOfDay(),
])
->get()
->groupBy('adjustment_type')
->map(function ($items) {
    return [
        'type' => $items->first()->adjustment_type_label,
        'count' => $items->count(),
        'total_amount' => abs($items->sum('adjustment_amount')),
        'pending' => $items->where('status', 'pending')->count(),
        'processed' => $items->where('status', 'processed')->count(),
    ];
})
->values();
```

### Outstanding vs Processed

```php
$outstanding = PaymentAdjustment::pending()
    ->groupBy('adjustment_type')
    ->map(fn($items) => [
        'type' => $items->first()->adjustment_type_label,
        'count' => $items->count(),
        'total' => abs($items->sum('adjustment_amount')),
    ]);

$processed = PaymentAdjustment::processed()
    ->groupBy('adjustment_type')
    ->map(fn($items) => [
        'type' => $items->first()->adjustment_type_label,
        'count' => $items->count(),
        'total' => abs($items->sum('adjustment_amount')),
    ]);
```

---

## 2. Student-Level Reporting

### Student Adjustment History

```php
use App\Models\User;

$studentId = 5;

$history = PaymentAdjustment::where('student_id', $studentId)
    ->with('weeklyPayment', 'invoiceTransaction', 'refundTransaction')
    ->orderBy('created_at', 'desc')
    ->get()
    ->map(function ($adj) {
        return [
            'date' => $adj->created_at->format('d/m/Y'),
            'week' => $adj->weeklyPayment->week_number,
            'month' => $adj->weeklyPayment->month,
            'type' => $adj->adjustment_type_label,
            'amount' => $adj->adjustment_amount,
            'status' => $adj->status_label,
            'method' => $adj->handling_method_label,
        ];
    });
```

### Student Total Debt/Credit

```php
// Total shortage yang belum dibayar
$totalDebt = PaymentAdjustment::where('student_id', $studentId)
    ->shortage()
    ->where('status', 'pending')
    ->sum('adjustment_amount');

// Total credit balance
$creditBalance = StudentCreditBalance::where('student_id', $studentId)
    ->value('total_credit') ?? 0;

// Net position
$netPosition = $creditBalance - $totalDebt;
```

### Class-Level Summary

```php
$classSummary = PaymentAdjustment::pending()
    ->with('student')
    ->get()
    ->groupBy('student.class')
    ->map(function ($adjustments) {
        $shortage = $adjustments->where('adjustment_type', 'shortage')
            ->sum('adjustment_amount');
        
        $overpayment = abs($adjustments
            ->where('adjustment_type', 'overpayment')
            ->sum('adjustment_amount'));

        return [
            'class' => $adjustments->first()->student->class,
            'students_affected' => $adjustments->pluck('student_id')->unique()->count(),
            'total_shortage' => $shortage,
            'total_overpayment' => $overpayment,
            'total_adjustments' => $adjustments->count(),
        ];
    })
    ->values();
```

---

## 3. Financial Reporting

### Kas Report dengan Adjustments

```php
use App\Models\Transaction;

$startDate = now()->startOfMonth();
$endDate = now()->endOfMonth();

// Income dari adjustment
$adjustmentIncome = Transaction::where('type', 'income')
    ->where('description', 'like', '%penyesuaian%')
    ->whereBetween('date', [$startDate, $endDate])
    ->sum('amount');

// Expense dari adjustment (refund)
$adjustmentExpense = Transaction::where('type', 'expense')
    ->where('description', 'like', '%penyesuaian%')
    ->whereBetween('date', [$startDate, $endDate])
    ->sum('amount');

// Summary
$report = [
    'adjustment_income' => $adjustmentIncome,
    'adjustment_expense' => $adjustmentExpense,
    'net_adjustment' => $adjustmentIncome - $adjustmentExpense,
];
```

### Credit Balance Liability Report

```php
// Total kredit yang diberikan (liability)
$totalCreditLiability = StudentCreditBalance::sum('total_credit');

// Perbandingan dengan total kas
$kasReport = [
    'total_credit_liability' => $totalCreditLiability,
    'students_with_credit' => StudentCreditBalance::where('total_credit', '>', 0)->count(),
    'average_credit' => StudentCreditBalance::where('total_credit', '>', 0)->avg('total_credit'),
    'max_credit' => StudentCreditBalance::max('total_credit'),
];
```

---

## 4. Analytical Queries

### Trend Analysis

```php
$monthlyTrend = [];

for ($m = 1; $m <= 12; $m++) {
    $monthTrend = PaymentAdjustment::whereMonth('created_at', $m)
        ->whereYear('created_at', 2026)
        ->get();

    $monthlyTrend[] = [
        'month' => $m,
        'total_shortage' => $monthTrend->where('adjustment_type', 'shortage')->sum('adjustment_amount'),
        'total_overpayment' => abs($monthTrend->where('adjustment_type', 'overpayment')->sum('adjustment_amount')),
        'count' => $monthTrend->count(),
    ];
}
```

### Processing Time Analysis

```php
$processingStats = PaymentAdjustment::processed()
    ->whereNotNull('processed_at')
    ->get()
    ->map(function ($adj) {
        $daysToProcess = $adj->created_at->diffInDays($adj->processed_at);
        return [
            'id' => $adj->id,
            'days_to_process' => $daysToProcess,
            'type' => $adj->adjustment_type,
        ];
    });

$avgDaysToProcess = $processingStats->avg('days_to_process');
$maxDaysToProcess = $processingStats->max('days_to_process');
```

### Handling Method Distribution

```php
$handlingMethodStats = PaymentAdjustment::pending()
    ->groupBy('handling_method')
    ->map(function ($items) {
        return [
            'method' => $items->first()->handling_method_label,
            'count' => $items->count(),
            'total_amount' => abs($items->sum('adjustment_amount')),
            'percentage' => ($items->count() / PaymentAdjustment::pending()->count()) * 100,
        ];
    })
    ->values();
```

---

## 5. Audit & Compliance Queries

### Who Changed What

```php
$auditTrail = PaymentAdjustment::processed()
    ->with('detectedBy', 'processedBy')
    ->whereBetween('processed_at', [$startDate, $endDate])
    ->get()
    ->map(function ($adj) {
        return [
            'adjustment_id' => $adj->id,
            'student' => $adj->student->name,
            'detected_by' => $adj->detectedBy->name,
            'processed_by' => $adj->processedBy->name,
            'detected_at' => $adj->created_at,
            'processed_at' => $adj->processed_at,
            'type' => $adj->adjustment_type,
            'amount' => $adj->adjustment_amount,
            'handling_method' => $adj->handling_method,
            'notes' => $adj->notes,
        ];
    });
```

### Cancelled Adjustments Investigation

```php
$cancelledAdjustments = PaymentAdjustment::where('status', 'cancelled')
    ->with('student', 'detectedBy')
    ->get()
    ->map(function ($adj) {
        return [
            'id' => $adj->id,
            'student' => $adj->student->name,
            'reason' => $adj->notes,
            'detected_by' => $adj->detectedBy->name,
            'detected_at' => $adj->created_at,
            'cancelled_at' => $adj->updated_at,
        ];
    });
```

### Data Integrity Checks

```php
// Check: Apakah semua adjustment memiliki weekly_payment yang valid
$orphanedAdjustments = PaymentAdjustment::leftJoin('weekly_payments', 'payment_adjustments.weekly_payment_id', '=', 'weekly_payments.id')
    ->whereNull('weekly_payments.id')
    ->get();

// Check: Apakah calculation benar (adjustment_amount = current_nominal - original_amount)
$incorrectCalculations = PaymentAdjustment::all()
    ->filter(function ($adj) {
        $calculated = $adj->current_nominal - $adj->original_amount;
        return $adj->adjustment_amount != $calculated;
    });

// Check: Apakah ada duplicate adjustments per weekly_payment
$duplicateAdjustments = PaymentAdjustment::all()
    ->groupBy('weekly_payment_id')
    ->filter(fn($group) => $group->count() > 1);
```

---

## 6. Export Functions

### Generate CSV Report

```php
use League\Csv\Writer;
use SplFileObject;

function exportAdjustmentsCsv($filePath, $month, $year)
{
    $adjustments = PaymentAdjustment::whereBetween('created_at', [
        now()->setDate($year, $month, 1),
        now()->setDate($year, $month, 30)->endOfDay(),
    ])
    ->with('student', 'weeklyPayment', 'detectedBy', 'processedBy')
    ->get();

    $csv = Writer::createFromPath($filePath, 'w');
    
    // Headers
    $csv->insertOne([
        'No',
        'Tanggal',
        'Siswa',
        'Minggu',
        'Bulan',
        'Tipe',
        'Nominal Awal',
        'Nominal Baru',
        'Selisih',
        'Status',
        'Metode',
        'Dideteksi Oleh',
        'Diproses Oleh',
        'Catatan',
    ]);

    // Data
    foreach ($adjustments as $index => $adj) {
        $csv->insertOne([
            $index + 1,
            $adj->created_at->format('d/m/Y H:i'),
            $adj->student->name,
            $adj->weeklyPayment->week_number,
            $adj->weeklyPayment->month . '/' . $adj->weeklyPayment->year,
            $adj->adjustment_type_label,
            $adj->original_amount,
            $adj->current_nominal,
            $adj->adjustment_amount,
            $adj->status_label,
            $adj->handling_method_label,
            $adj->detectedBy->name,
            $adj->processedBy?->name ?? '-',
            $adj->notes,
        ]);
    }

    return $csv;
}
```

### Generate Excel Report

```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generateAdjustmentExcel($month, $year)
{
    $adjustments = PaymentAdjustment::whereBetween('created_at', [
        now()->setDate($year, $month, 1),
        now()->setDate($year, $month, 30)->endOfDay(),
    ])
    ->with('student', 'weeklyPayment')
    ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Headers
    $headers = ['No', 'Siswa', 'Minggu', 'Tipe', 'Selisih', 'Status', 'Metode'];
    $sheet->fromArray($headers, null, 'A1');

    // Data
    $row = 2;
    foreach ($adjustments as $index => $adj) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $adj->student->name);
        $sheet->setCellValue('C' . $row, 'Minggu ' . $adj->weeklyPayment->week_number);
        $sheet->setCellValue('D' . $row, $adj->adjustment_type_label);
        $sheet->setCellValue('E' . $row, 'Rp ' . number_format($adj->adjustment_amount, 0, ',', '.'));
        $sheet->setCellValue('F' . $row, $adj->status_label);
        $sheet->setCellValue('G' . $row, $adj->handling_method_label);
        $row++;
    }

    // Auto-fit columns
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = "Adjustment_Kas_{$month}_{$year}.xlsx";
    $path = storage_path("app/reports/{$fileName}");
    
    $writer->save($path);
    return $path;
}
```

---

## 7. Real-Time Dashboard Queries

### Dashboard Summary Widget

```php
function getDashboardSummary()
{
    return [
        'total_pending_adjustments' => PaymentAdjustment::pending()->count(),
        'total_shortage_pending' => PaymentAdjustment::pending()
            ->shortage()
            ->sum('adjustment_amount'),
        'total_overpayment_pending' => abs(PaymentAdjustment::pending()
            ->overpayment()
            ->sum('adjustment_amount')),
        'total_credit_balance' => StudentCreditBalance::sum('total_credit'),
        'students_with_shortage' => PaymentAdjustment::pending()
            ->shortage()
            ->distinct('student_id')
            ->count(),
        'students_with_credit' => StudentCreditBalance::where('total_credit', '>', 0)->count(),
        'processed_this_month' => PaymentAdjustment::processed()
            ->whereMonth('processed_at', now()->month)
            ->count(),
        'avg_days_to_process' => PaymentAdjustment::processed()
            ->whereNotNull('processed_at')
            ->get()
            ->map(fn($adj) => $adj->created_at->diffInDays($adj->processed_at))
            ->avg(),
    ];
}
```

---

## Performance Optimization Tips

### Indexes untuk Query Cepat

Sudah ada di migration, tapi pastikan:
```sql
CREATE INDEX idx_payment_adjustments_student_status 
ON payment_adjustments(student_id, status);

CREATE INDEX idx_payment_adjustments_created_at 
ON payment_adjustments(created_at);

CREATE INDEX idx_payment_adjustments_type 
ON payment_adjustments(adjustment_type);
```

### Use Eager Loading

```php
// ❌ N+1 Query Problem
$adjustments = PaymentAdjustment::all();
foreach ($adjustments as $adj) {
    echo $adj->student->name; // Extra query setiap loop
}

// ✅ Optimized
$adjustments = PaymentAdjustment::with('student')->get();
foreach ($adjustments as $adj) {
    echo $adj->student->name; // No extra query
}
```

### Cache untuk Expensive Queries

```php
$summary = Cache::remember('payment_adjustments_summary', 60, function () {
    return [
        'total_pending' => PaymentAdjustment::pending()->count(),
        'total_amount' => PaymentAdjustment::pending()->sum('adjustment_amount'),
    ];
});
```

---

## Testing Queries

Gunakan Tinker untuk test queries:

```bash
php artisan tinker
```

```php
> $adj = PaymentAdjustment::with('student', 'weeklyPayment')->first();
> $adj->adjustment_type_label
> PaymentAdjustment::pending()->shortage()->sum('adjustment_amount')
> StudentCreditBalance::where('total_credit', '>', 0)->count()
```

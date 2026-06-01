<?php

use App\Models\User;
use App\Services\PaymentAdjustmentService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('payment-adjustments:reconcile {month?} {year?}', function ($month = null, $year = null) {
    /** @var PaymentAdjustmentService $service */
    $service = app(PaymentAdjustmentService::class);

    $detectedBy = User::find(1) ?? User::where('role', 'bendahara')->first() ?? User::first();
    if (!$detectedBy) {
        $this->error('Tidak ada user valid untuk detected_by. Pastikan setidaknya ada satu user.');
        return 1;
    }

    if ($month !== null && $year !== null) {
        $month = (int) $month;
        $year = (int) $year;

        $nominal = \App\Models\KasSetting::where('month', $month)->where('year', $year)->value('nominal');
        if ($nominal === null) {
            $this->error("KasSetting untuk {$month}/{$year} belum diatur. Tidak ada adjustment yang dibuat.");
            return 1;
        }

        $this->info("Menjalankan reconcile untuk bulan {$month}/{$year}...");
        $created = $service->reconcileAdjustments(
            month: $month,
            year: $year,
            currentNominal: (float) $nominal,
            detectedBy: $detectedBy,
        );

        $this->info("Adjustment baru dibuat: {$created}");
        return 0;
    }

    $this->info('Menjalankan reconcile untuk semua bulan dengan pembayaran lunas...');

    $months = \App\Models\WeeklyPayment::select('month', 'year')
        ->where('status', 'paid')
        ->groupBy('month', 'year')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    $totalCreated = 0;
    foreach ($months as $period) {
        $nominal = \App\Models\KasSetting::where('month', $period->month)->where('year', $period->year)->value('nominal');
        if ($nominal === null) {
            $this->warn("Lewati {$period->month}/{$period->year}: nominal belum diatur.");
            continue;
        }

        $this->info("Reconcile {$period->month}/{$period->year} dengan nominal {$nominal}...");
        $created = $service->reconcileAdjustments(
            month: $period->month,
            year: $period->year,
            currentNominal: (float) $nominal,
            detectedBy: $detectedBy,
        );

        $totalCreated += $created;
        $this->info("  Adjustment baru: {$created}");
    }

    $this->info("Selesai. Total adjustment baru dibuat: {$totalCreated}");
    return 0;
})->purpose('Reconcile paid weekly payments against current KasSetting nominal and create missing adjustments.');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

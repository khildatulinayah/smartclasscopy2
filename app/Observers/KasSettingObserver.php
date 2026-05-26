<?php

namespace App\Observers;

use App\Models\KasSetting;
use App\Services\PaymentAdjustmentService;
use Illuminate\Support\Facades\Auth;

/**
 * Observer untuk KasSetting
 * 
 * Mendeteksi perubahan nominal dan auto-create adjustment
 * 
 * Registrasi di: app/Providers/AppServiceProvider.php
 * 
 *  public function boot(): void
 *  {
 *      KasSetting::observe(KasSettingObserver::class);
 *  }
 */
class KasSettingObserver
{
    private PaymentAdjustmentService $adjustmentService;

    public function __construct(PaymentAdjustmentService $adjustmentService)
    {
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * Handle the KasSetting "updating" event.
     * 
     * Dipanggil sebelum update, untuk capture nilai original
     */
    public function updating(KasSetting $kasSetting): void
    {
        // Store original nominal untuk comparison
        $kasSetting->__originalNominal = $kasSetting->getOriginal('nominal');
    }

    /**
     * Handle the KasSetting "updated" event.
     * 
     * Auto-detect dan create adjustment jika nominal berubah
     */
    public function updated(KasSetting $kasSetting): void
    {
        $oldNominal = $kasSetting->__originalNominal ?? $kasSetting->nominal;
        $newNominal = $kasSetting->nominal;

        // Skip jika tidak ada perubahan
        if ($oldNominal == $newNominal) {
            return;
        }

        try {
            // Get current user, fallback ke system user jika tidak ada
            $detectedBy = Auth::user();
            if (!$detectedBy) {
                // Jika tidak ada user yang login (e.g., dari command), gunakan system user
                // Atau bisa throw error tergantung requirement
                \Log::warning('KasSetting diubah tapi tidak ada user yang login', [
                    'month' => $kasSetting->month,
                    'year' => $kasSetting->year,
                    'old_nominal' => $oldNominal,
                    'new_nominal' => $newNominal,
                ]);
                return;
            }

            // Detect dan create adjustment
            $adjustments = $this->adjustmentService->detectAndCreateAdjustments(
                month: $kasSetting->month,
                year: $kasSetting->year,
                newNominal: $newNominal,
                oldNominal: $oldNominal,
                detectedBy: $detectedBy
            );

            if ($adjustments->isNotEmpty()) {
                \Log::info('Adjustment terdeteksi dan dibuat otomatis', [
                    'month' => $kasSetting->month,
                    'year' => $kasSetting->year,
                    'old_nominal' => $oldNominal,
                    'new_nominal' => $newNominal,
                    'adjustment_count' => $adjustments->count(),
                ]);

                // Optional: Send notification ke bendahara
                // Notification::send(bendahara_users, new PaymentAdjustmentDetectedNotification($adjustments));
            }
        } catch (\Exception $e) {
            \Log::error('Error saat detect adjustment di KasSettingObserver', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the KasSetting "created" event.
     * 
     * Tidak perlu detect adjustment saat create (belum ada pembayaran)
     */
    public function created(KasSetting $kasSetting): void
    {
        // Tidak perlu action saat create
    }
}

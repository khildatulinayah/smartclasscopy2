<?php

namespace App\Services;

use App\Models\KasSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Service untuk mengelola KasSetting dan auto-detect adjustment
 * 
 * Flow:
 * 1. Bendahara ubah nominal KasSetting
 * 2. Observer mendeteksi perubahan
 * 3. Service ini membuat adjustments otomatis
 * 
 * Bisa dipanggil:
 * - Via Observer (otomatis saat KasSetting.save())
 * - Via Controller/Command (manual)
 */
class KasSettingService
{
    private PaymentAdjustmentService $adjustmentService;

    public function __construct(PaymentAdjustmentService $adjustmentService)
    {
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * Update nominal kas dan auto-detect adjustment
     * 
     * @param int $month
     * @param int $year
     * @param float $newNominal
     * @param User|null $detectedBy Default: current auth user
     * @return array ['kas_setting' => KasSetting, 'adjustments' => Collection]
     */
    public function updateNominalWithAdjustmentDetection(
        int $month,
        int $year,
        float $newNominal,
        ?User $detectedBy = null
    ): array {
        // Default: detected by current user
        if ($detectedBy === null) {
            $detectedBy = Auth::user();
            if (!$detectedBy || !$detectedBy instanceof User) {
                throw new \InvalidArgumentException('Tidak ada user yang terautentikasi');
            }
        }

        // Get old nominal
        $oldKasSetting = KasSetting::where('month', $month)
            ->where('year', $year)
            ->first();
        
        $oldNominal = $oldKasSetting?->nominal ?? 0;

        // Update atau create KasSetting
        $kasSetting = KasSetting::updateOrCreate(
            ['month' => $month, 'year' => $year],
            ['nominal' => $newNominal]
        );

        // Detect adjustment jika ada perubahan
        $adjustments = $this->adjustmentService->detectAndCreateAdjustments(
            month: $month,
            year: $year,
            newNominal: $newNominal,
            oldNominal: $oldNominal,
            detectedBy: $detectedBy
        );

        return [
            'kas_setting' => $kasSetting,
            'old_nominal' => $oldNominal,
            'new_nominal' => $newNominal,
            'adjustments' => $adjustments,
        ];
    }

    /**
     * Get nominal untuk bulan/tahun tertentu
     * Jika tidak ada, return null
     * 
     * @param int $month
     * @param int $year
     * @return float|null
     */
    public function getNominal(int $month, int $year): ?float
    {
        $setting = KasSetting::where('month', $month)
            ->where('year', $year)
            ->first();

        return $setting?->nominal;
    }

    /**
     * Get atau set default nominal jika tidak ada untuk bulan/tahun
     * 
     * @param int $month
     * @param int $year
     * @param float|null $defaultNominal
     * @return float
     */
    public function getNominalOrDefault(int $month, int $year, ?float $defaultNominal = null): float
    {
        $nominal = $this->getNominal($month, $year);

        if ($nominal === null && $defaultNominal !== null) {
            KasSetting::create([
                'month' => $month,
                'year' => $year,
                'nominal' => $defaultNominal,
            ]);
            return $defaultNominal;
        }

        return $nominal ?? 0;
    }

    /**
     * Get all kas settings untuk tahun tertentu
     * 
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNominalsForYear(int $year)
    {
        return KasSetting::where('year', $year)
            ->orderBy('month')
            ->get();
    }

    /**
     * Validate nominal value
     * 
     * @param float $nominal
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateNominal(float $nominal): array
    {
        $errors = [];

        if ($nominal <= 0) {
            $errors[] = 'Nominal harus lebih dari 0';
        }

        if ($nominal > 999999999) {
            $errors[] = 'Nominal terlalu besar (maksimal 999.999.999)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get nominal terakhir yang diset (regardless of month/year)
     * Berguna untuk setting default atau auto-fill
     * 
     * @return float|null
     */
    public function getLastSetNominal(): ?float
    {
        return KasSetting::latest('updated_at')
            ->value('nominal');
    }

    /**
     * Compare nominal trend untuk analisis
     * 
     * @param int $year
     * @return array
     */
    public function getNominalTrend(int $year): array
    {
        $settings = KasSetting::where('year', $year)
            ->orderBy('month')
            ->get(['month', 'nominal']);

        return $settings->map(function ($setting) {
            return [
                'month' => $setting->month,
                'nominal' => $setting->nominal,
                'formatted' => 'Rp ' . number_format($setting->nominal, 0, ',', '.'),
            ];
        })->toArray();
    }
}

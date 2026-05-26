<?php

namespace App\Http\Controllers;

use App\Models\PaymentAdjustment;
use App\Models\StudentCreditBalance;
use App\Services\KasSettingService;
use App\Services\PaymentAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controller untuk mengelola payment adjustments
 * 
 * Endpoints:
 * - GET /api/payment-adjustments
 * - GET /api/payment-adjustments/{id}
 * - POST /api/payment-adjustments/{id}/process-shortage-invoice
 * - POST /api/payment-adjustments/{id}/process-overpayment-credit
 * - POST /api/payment-adjustments/{id}/process-overpayment-refund
 * - POST /api/payment-adjustments/{id}/cancel
 * - GET /api/payment-adjustments/summary
 * - POST /kas-setting-update (Update KasSetting & auto-detect)
 */
class PaymentAdjustmentController extends Controller
{
    private PaymentAdjustmentService $adjustmentService;
    private KasSettingService $kasSettingService;

    public function __construct(
        PaymentAdjustmentService $adjustmentService,
        KasSettingService $kasSettingService
    ) {
        $this->adjustmentService = $adjustmentService;
        $this->kasSettingService = $kasSettingService;
    }

    /**
     * GET /api/payment-adjustments
     * 
     * List semua adjustment dengan filter optional
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaymentAdjustment::with('student', 'weeklyPayment', 'detectedBy', 'processedBy');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by adjustment type
        if ($request->has('type')) {
            $query->where('adjustment_type', $request->type);
        }

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by handling method
        if ($request->has('handling_method')) {
            $query->where('handling_method', $request->handling_method);
        }

        $adjustments = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $adjustments,
        ]);
    }

    /**
     * GET /api/payment-adjustments/{id}
     * 
     * Detail adjustment
     */
    public function show(PaymentAdjustment $adjustment): JsonResponse
    {
        $adjustment->load('student', 'weeklyPayment', 'detectedBy', 'processedBy', 
                         'invoiceTransaction', 'refundTransaction', 'creditTransaction');

        $detail = $this->adjustmentService->getAdjustmentDetail($adjustment);

        return response()->json([
            'success' => true,
            'data' => $detail,
        ]);
    }

    /**
     * POST /api/payment-adjustments/{id}/process-shortage-invoice
     * 
     * Process shortage adjustment dengan method "Invoice Terpisah"
     */
    public function processShortageAsInvoice(Request $request, PaymentAdjustment $adjustment): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if (!$adjustment->isShortage()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment ini bukan tipe shortage (kurang bayar)',
                ], 422);
            }

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment harus dalam status pending',
                ], 422);
            }

            $transaction = $this->adjustmentService->processShortageAsInvoice(
                adjustment: $adjustment,
                processedBy: auth()->user(),
                notes: $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibuat',
                'data' => [
                    'adjustment' => $adjustment->refresh(),
                    'transaction' => $transaction,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/payment-adjustments/{id}/process-overpayment-credit
     * 
     * Process overpayment adjustment dengan method "Saldo Kredit"
     */
    public function processOverpaymentAsCredit(Request $request, PaymentAdjustment $adjustment): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if (!$adjustment->isOverpayment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment ini bukan tipe overpayment (kelebihan bayar)',
                ], 422);
            }

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment harus dalam status pending',
                ], 422);
            }

            $creditBalance = $this->adjustmentService->processOverpaymentAsCredit(
                adjustment: $adjustment,
                processedBy: auth()->user(),
                notes: $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Kelebihan dibayar disimpan sebagai saldo kredit',
                'data' => [
                    'adjustment' => $adjustment->refresh(),
                    'credit_balance' => $creditBalance,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/payment-adjustments/{id}/process-overpayment-refund
     * 
     * Process overpayment adjustment dengan method "Pengembalian Dana"
     */
    public function processOverpaymentAsRefund(Request $request, PaymentAdjustment $adjustment): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if (!$adjustment->isOverpayment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment ini bukan tipe overpayment (kelebihan bayar)',
                ], 422);
            }

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment harus dalam status pending',
                ], 422);
            }

            $transaction = $this->adjustmentService->processOverpaymentAsRefund(
                adjustment: $adjustment,
                processedBy: auth()->user(),
                notes: $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengembalian dana berhasil dibuat',
                'data' => [
                    'adjustment' => $adjustment->refresh(),
                    'transaction' => $transaction,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/payment-adjustments/{id}/process-shortage-unpaid
     * 
     * Process shortage adjustment dengan method "Tambah Tagihan" (unpaid)
     * 
     * Ini hanya mark sebagai processed, tidak membuat transaksi
     */
    public function processShortageAsUnpaid(Request $request, PaymentAdjustment $adjustment): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if (!$adjustment->isShortage()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment ini bukan tipe shortage (kurang bayar)',
                ], 422);
            }

            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Adjustment harus dalam status pending',
                ], 422);
            }

            $this->adjustmentService->processShortageAsUnpaid(
                adjustment: $adjustment,
                processedBy: auth()->user(),
                notes: $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Kekurangan disimpan sebagai tagihan untuk pembayaran mendatang',
                'data' => [
                    'adjustment' => $adjustment->refresh(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/payment-adjustments/{id}/cancel
     * 
     * Cancel adjustment
     */
    public function cancel(Request $request, PaymentAdjustment $adjustment): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            if ($adjustment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya adjustment yang pending yang bisa dibatalkan',
                ], 422);
            }

            $this->adjustmentService->cancelAdjustment(
                adjustment: $adjustment,
                reason: $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Adjustment berhasil dibatalkan',
                'data' => [
                    'adjustment' => $adjustment->refresh(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/payment-adjustments/summary
     * 
     * Summary adjustment per student
     */
    public function summary(): JsonResponse
    {
        $summary = $this->adjustmentService->getSummaryPerStudent();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * POST /bendahara/kas-setting-update
     * 
     * Update KasSetting dan auto-detect adjustment
     * 
     * Request:
     * {
     *   "month": 5,
     *   "year": 2026,
     *   "nominal": 8000
     * }
     */
    public function updateKasSettingWithAdjustment(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'nominal' => 'required|numeric|min:1|max:999999999',
        ]);

        try {
            $validation = $this->kasSettingService->validateNominal($request->nominal);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal tidak valid',
                    'errors' => $validation['errors'],
                ], 422);
            }

            $result = $this->kasSettingService->updateNominalWithAdjustmentDetection(
                month: $request->month,
                year: $request->year,
                newNominal: $request->nominal,
                detectedBy: auth()->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Nominal kas berhasil diperbarui',
                'data' => [
                    'old_nominal' => $result['old_nominal'],
                    'new_nominal' => $result['new_nominal'],
                    'adjustments_created' => $result['adjustments']->count(),
                    'adjustments' => $result['adjustments']->map(function ($adj) {
                        return [
                            'id' => $adj->id,
                            'student_name' => $adj->student->name,
                            'type' => $adj->adjustment_type_label,
                            'amount' => $adj->adjustment_amount,
                            'status' => $adj->status_label,
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nominal kas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/student-credit-balances
     * 
     * Daftar student dengan credit balance
     */
    public function creditBalances(): JsonResponse
    {
        $balances = StudentCreditBalance::hasCredit()
            ->with('student:id,name,email')
            ->orderBy('total_credit', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $balances,
        ]);
    }
}

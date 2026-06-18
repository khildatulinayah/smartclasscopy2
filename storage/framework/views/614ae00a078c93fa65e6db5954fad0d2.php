<!-- Modal Payment Adjustment -->
<div id="paymentAdjustmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Payment Adjustment (Semua)</h3>
            <button type="button" onclick="closePaymentAdjustmentModal()" class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-semibold">
                Tutup
            </button>
        </div>

        <div class="p-6">
            <?php if(empty($pendingAdjustments) || $pendingAdjustments->count() === 0): ?>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center text-sm text-gray-700">
                    Tidak ada penyesuaian.
                </div>
            <?php else: ?>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6 text-center">
                    <div class="text-sm font-semibold text-indigo-800">
                        <?php echo e($pendingAdjustments->count()); ?> penyesuaian
                    </div>
                    <div class="text-xs text-gray-600">Klik tombol untuk Lunasi/Kembalikan</div>
                </div>

                <div class="space-y-4">
                    <?php $__currentLoopData = $pendingAdjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $studentName = optional($adj->student)->name ?? 'Siswa';
                            $weeklyPayment = $adj->weeklyPayment;
                            $weekLabel = $weeklyPayment ? 'Minggu '.$weeklyPayment->week_number : '-';
                            $dateLabel = $weeklyPayment && isset($weeklyPayment->wednesday_date)
                                ? optional($weeklyPayment->wednesday_date)->locale('id')->translatedFormat('d M Y')
                                : null;
                            $amountText = $adj->formatted_amount;
                            $isShortage = $adj->isShortage();
                            $statusLabel = method_exists($adj, 'isProcessed') && $adj->isProcessed() ? 'Processed' : 'Pending';
                        ?>

                        <div class="bg-white border border-gray-200 rounded-lg p-4">

                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-600"><?php echo e($weekLabel); ?> • <?php echo e($studentName); ?></div>
                                    <div class="mt-1 flex items-center gap-2">
                                        <?php if($isShortage): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-900 text-xs font-semibold">Kurang <?php echo e($amountText); ?></span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">Lebih <?php echo e($amountText); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-800">Nominal Adjustment</div>
                                    <div class="text-lg font-bold text-gray-900">Rp <?php echo e(number_format($adj->adjustment_amount, 0, ',', '.')); ?></div>
                                </div>
                            </div>

                            <div class="mt-4 flex gap-3">
                                <?php if($isShortage): ?>
                                    <form method="POST" action="<?php echo e(route('bendahara.adjustment.shortage', $adj->id)); ?>" class="flex-1">
                                        <?php echo csrf_field(); ?>
                                        <button class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600 transition-colors">
                                            Lunasi
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo e(route('bendahara.adjustment.refund', $adj->id)); ?>" class="flex-1">
                                        <?php echo csrf_field(); ?>
                                        <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
                                            Kembalikan
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/payment-adjustment-modal.blade.php ENDPATH**/ ?>
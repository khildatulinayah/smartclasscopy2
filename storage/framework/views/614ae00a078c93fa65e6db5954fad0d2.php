<!-- Modal Payment Adjustment -->
<div id="paymentAdjustmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800">Payment Adjustment</h3>
            <button type="button" onclick="closePaymentAdjustmentModal()" class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-semibold">
                Tutup
            </button>
        </div>

        <div class="p-6">
            <?php
                $allAdjustments = $pendingAdjustments ?? collect();
                $pendingOnly = $allAdjustments->where('status', 'pending')->values();
                $historyOnly = $allAdjustments->whereIn('status', ['processed', 'cancelled'])->values();
            ?>

            <?php if($allAdjustments->isEmpty()): ?>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center text-sm text-gray-700">
                    Tidak ada penyesuaian.
                </div>
            <?php else: ?>
                
                <div class="mb-3">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Perlu Tindakan</h4>
                </div>

                <?php if($pendingOnly->isEmpty()): ?>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 text-center text-sm text-gray-600">
                        Tidak ada penyesuaian yang menunggu tindakan.
                    </div>
                <?php else: ?>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4 text-center">
                        <div class="text-sm font-semibold text-indigo-800">
                            <?php echo e($pendingOnly->count()); ?> penyesuaian menunggu tindakan
                        </div>
                        <div class="text-xs text-gray-600">Klik tombol untuk Lunasi/Kembalikan</div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <?php $__currentLoopData = $pendingOnly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                // Relasi yang di-eager-load dari controller adalah weeklyPayment.student.
                                $weeklyPayment = $adj->weeklyPayment;
                                $studentName = optional($weeklyPayment->student ?? null)->name
                                    ?? optional($adj->student ?? null)->name
                                    ?? 'Siswa';
                                $weekLabel = $weeklyPayment ? 'Minggu '.$weeklyPayment->week_number : '-';
                                $amountText = $adj->formatted_amount;
                                $isShortage = $adj->isShortage();
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

                
                <?php if($historyOnly->isNotEmpty()): ?>
                    <div class="mb-3">
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Riwayat</h4>
                    </div>

                    <div class="space-y-3">
                        <?php $__currentLoopData = $historyOnly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $weeklyPayment = $adj->weeklyPayment;
                                $studentName = optional($weeklyPayment->student ?? null)->name
                                    ?? optional($adj->student ?? null)->name
                                    ?? 'Siswa';
                                $weekLabel = $weeklyPayment ? 'Minggu '.$weeklyPayment->week_number : '-';
                                $amountText = $adj->formatted_amount;
                                $isShortage = $adj->isShortage();
                                $isCancelled = $adj->status === 'cancelled';
                            ?>

                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 opacity-90">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600"><?php echo e($weekLabel); ?> • <?php echo e($studentName); ?></div>
                                        <div class="mt-1 flex items-center gap-2 flex-wrap">
                                            <?php if($isShortage): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-900 text-xs font-semibold">Kurang <?php echo e($amountText); ?></span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">Lebih <?php echo e($amountText); ?></span>
                                            <?php endif; ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full <?php echo e($isCancelled ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?> text-xs font-semibold">
                                                <?php echo e($adj->status_label); ?>

                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-gray-500">Nominal Adjustment</div>
                                        <div class="text-lg font-bold text-gray-700">Rp <?php echo e(number_format($adj->adjustment_amount, 0, ',', '.')); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/payment-adjustment-modal.blade.php ENDPATH**/ ?>
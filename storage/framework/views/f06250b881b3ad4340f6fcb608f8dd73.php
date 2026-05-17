<?php $__env->startSection('title', 'Pengaturan Kas'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Kas</h1>
        <p class="text-sm text-gray-500 mt-1">
            Atur nominal kas per bulan. Nominal dipakai saat transaksi baru dibuat untuk bulan tersebut.
        </p>

        <?php if(session('success')): ?>
            <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <?php if($isCurrentMonth): ?>
            <div class="mt-6 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800 text-sm">
                Mengubah nominal bulan ini tidak mempengaruhi pembayaran yang sudah dilakukan.
            </div>
        <?php endif; ?>

        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
            <p class="text-sm text-blue-700">
                Nominal saat ini:
                <span class="font-semibold">Rp <?php echo e(number_format($currentNominal, 0, ',', '.')); ?></span>
                untuk <?php echo e(\Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->locale('id')->translatedFormat('F Y')); ?>

            </p>
        </div>

        <form action="<?php echo e(route('bendahara.kas.settings')); ?>" method="GET" class="mt-6 grid md:grid-cols-2 gap-4">
            <div>
                <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select id="filter_month" name="month" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required>
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($selectedMonth == $m ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create(2026, $m, 1)->locale('id')->translatedFormat('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select id="filter_year" name="year" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required>
                    <?php for($y = now()->year - 1; $y <= now()->year + 2; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($selectedYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                    Tampilkan Bulan
                </button>
            </div>
        </form>

        <form action="<?php echo e(route('bendahara.kas.settings.update')); ?>" method="POST" class="mt-6 space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="month" value="<?php echo e($selectedMonth); ?>">
            <input type="hidden" name="year" value="<?php echo e($selectedYear); ?>">

            <div>
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal Kas Baru (Rp)</label>
                <input
                    type="number"
                    id="nominal"
                    name="nominal"
                    min="0"
                    step="1"
                    required
                    value="<?php echo e(old('nominal', $currentNominal)); ?>"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 5000"
                >
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    Simpan Pengaturan
                </button>
                <a href="<?php echo e(route('bendahara.weekly.payments')); ?>" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.bendahara', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views\bendahara\kas-settings.blade.php ENDPATH**/ ?>
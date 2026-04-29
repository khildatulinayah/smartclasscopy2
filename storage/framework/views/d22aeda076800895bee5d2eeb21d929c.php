

<?php $__env->startSection('content'); ?>

<div class="bg-white p-6 rounded-lg shadow-md">

    <h2 class="text-xl font-bold mb-4 text-center">Pilih Periode Laporan</h2>
    <p class="text-sm text-gray-600 mb-6 text-center">Absensi Siswa</p>

    <form method="GET" action="<?php echo e(route('sekretaris.laporan')); ?>" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-2">Bulan:</label>
            <select name="bulan" class="w-full p-2 border border-gray-300 rounded-md">
                <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo e($i); ?>" <?php echo e($i == date('n') ? 'selected' : ''); ?>>
                        <?php echo e($i); ?>

                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Tahun:</label>
            <input type="number" name="tahun" value="<?php echo e(date('Y')); ?>" 
                   class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="text-center">
            <button type="submit" 
                    class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
                Lihat Laporan
            </button>
        </div>
    </form>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/sekretaris/laporan-filter.blade.php ENDPATH**/ ?>
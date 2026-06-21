<?php $__env->startSection('title', 'Pengaturan Kas'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.bendahara-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
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
        </main>
    </div>
</div>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
.sidebar-header { padding: 24px 20px; border-bottom: 1px solid #e2e8f0; }
.logo { display: flex; align-items: center; gap: 12px; }
.logo-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
.logo-text { font-size: 20px; font-weight: 700; color: #1e293b; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #64748b; text-decoration: none; transition: all 0.2s ease; border-radius: 0 8px 8px 0; margin: 0 12px; }
.nav-item:hover { background: #f8fafc; color: #3b82f6; }
.nav-item.active { background: #eff6ff; color: #3b82f6; font-weight: 600; }
.nav-icon { width: 20px; height: 20px; }
.sidebar-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; }
.user-profile-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.user-avatar-mini { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }
.user-name-mini { font-size: 13px; font-weight: 600; color: #1e293b; }
.user-role-mini { font-size: 11px; color: #64748b; }
.logout-form { display: block; }
.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.logout-btn:hover { background: #fecaca; }
.logout-icon { width: 16px; height: 16px; }
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.main-content { flex: 1; padding: 32px; overflow-y: auto; }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/kas-settings.blade.php ENDPATH**/ ?>
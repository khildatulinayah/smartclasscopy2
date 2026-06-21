<?php $__env->startSection('title', 'Laporan Absensi'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <?php echo $__env->make('components.sekretaris-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Laporan Absensi Siswa</h1>
                    <p class="greeting-subtitle">Generate dan cetak laporan absensi bulanan siswa</p>
                </div>
            </section>

            <!-- Report Generation Card -->
            <section class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-12">
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-purple-500 to-indigo-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Laporan Absensi Bulanan</h2>
                            <p class="text-gray-600">Laporan lengkap kehadiran siswa per bulan dengan statistik detail</p>
                        </div>
                    </div>

                    <form id="laporan-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bulan & Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="month" id="laporan-month" class="form-select" required>
                                    <option value="">Pilih Bulan</option>
                                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($num); ?>" <?php echo e($currentMonth == $num ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select name="year" id="laporan-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e($currentYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakLaporan()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"></path>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="downloadLaporanPDF()" class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview PDF
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Note: Section informasi/tampilan dekoratif diperkecil agar halaman laporan tetap ringan untuk banyak laporan -->
            <section class="mb-12">
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <p class="text-gray-600 text-sm">
                        Pilih bulan & tahun, lalu klik <span class="font-semibold">Cetak</span> atau <span class="font-semibold">Preview PDF</span>.
                    </p>
                </div>
            </section>

        </main>
    </div>
</div>

<script>
function cetakLaporan() {
    const month = document.getElementById('laporan-month').value;
    const year = document.getElementById('laporan-year').value;
    if (month && year) {
        window.open(`<?php echo e(route('sekretaris.laporan.cetak', ['month' => ':month', 'year' => ':year'])); ?>`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadLaporanPDF() {
    const month = document.getElementById('laporan-month').value;
    const year = document.getElementById('laporan-year').value;
    if (month && year) {
        window.open(`<?php echo e(route('sekretaris.laporan.pdf', ['month' => ':month', 'year' => ':year'])); ?>`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}
</script>
<?php $__env->stopSection(); ?>

<!-- Dashboard CSS -->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
.sidebar-header { padding: 24px 20px; border-bottom: 1px solid #e2e8f0; }
.logo { display: flex; align-items: center; gap: 12px; }
.logo-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
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
.greeting-section { margin-bottom: 32px; }
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.report-card { backdrop-filter: blur(10px); }
.form-select { @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 bg-white shadow-sm hover:shadow-md; background-image: linear-gradient(45deg, transparent 50%, #e5e7eb 0), linear-gradient(135deg, #e5e7eb 50%, transparent 0); background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px); background-size: 5px 5px, 5px 5px; background-repeat: no-repeat; appearance: none; }
.main-content h1, .main-content h2 { font-weight: 800; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
</style>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/sekretaris/laporan.blade.php ENDPATH**/ ?>
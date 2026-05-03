<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.sekretaris-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
<section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Selamat <?php echo e(\Carbon\Carbon::now()->hour < 12 ? 'Pagi' : (\Carbon\Carbon::now()->hour < 15 ? 'Siang' : (\Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam'))); ?>, <?php echo e(auth()->user()->name); ?>!</h1>
                    <p class="greeting-subtitle">Kelola absensi siswa dengan mudah dan akurat</p>
                </div>
            </section>

            <section class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon blue"><svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                    </svg></div>
                    <h3 class="feature-title">Absensi Harian</h3>
                    <p class="feature-description">Input dan update absensi siswa hari ini</p>
                    <a href="<?php echo e(route('sekretaris.absensi')); ?>" class="feature-btn">Input Absensi</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
                    <h3 class="feature-title">Rekap Absensi</h3>
                    <p class="feature-description">Lihat ringkasan dan tracker bulanan</p>
                    <a href="<?php echo e(route('sekretaris.tracker')); ?>" class="feature-btn">Lihat Rekap</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon orange"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                    <h3 class="feature-title">Laporan</h3>
                    <p class="feature-description">Generate dan cetak laporan absensi</p>
                    <button class="feature-btn" disabled>Coming Soon</button>
                </div>
            </section>

            <?php 
                $attendancePercent = $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100) : 0; 
            ?>

            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon info"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><div class="stat-title">Total Siswa</div></div>
                        <div class="stat-value"><?php echo e($stats['total']); ?></div>
                        <div class="stat-description">Siswa aktif di kelas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon income"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div class="stat-title">Hadir Hari Ini</div></div>
                        <div class="stat-value"><?php echo e($stats['hadir']); ?></div>
                        <div class="stat-description">Siswa hadir <?php echo e($today); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon expense"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 0l-8 8-4-4-6 6"></path></svg></div><div class="stat-title">Izin</div></div>
                        <div class="stat-value"><?php echo e($stats['izin']); ?></div>
                        <div class="stat-description">Siswa berizin hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon remaining"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3v4m0 4h2a2 2 0 002 2v-4a2 2 0 00-2-2h-2a2 2 0 00-2-2v-4m2 6v-4h2a2 2 0 002 2v4a2 2 0 002 2z"></path></svg></div><div class="stat-title">Alpa</div></div>
                        <div class="stat-value"><?php echo e($stats['alpha']); ?></div>
                        <div class="stat-description">Siswa alpa hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon payment"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path></svg></div><div class="stat-title">Status Absensi</div></div>
                        <div class="progress-container">
                            <div class="progress-bar"><div class="progress-fill" style="width: <?php echo e($attendancePercent); ?>%"></div></div>
                            <div class="progress-text"><?php echo e($stats['hadir']); ?>/<?php echo e($stats['total']); ?> Hadir</div>
                        </div>
                        <div class="stat-details">
                            <div class="detail-item"><span class="detail-label">Hadir</span><span class="detail-value"><?php echo e($stats['hadir']); ?></span></div>
                            <div class="detail-item"><span class="detail-label">Alpa</span><span class="detail-value"><?php echo e($stats['alpha']); ?></span></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon balance"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></div><div class="stat-title">Informasi Hari Ini</div></div>
                        <div class="class-info">
                            <div class="info-row"><span class="info-label">Tanggal:</span><span class="info-value"><?php echo e(\Carbon\Carbon::parse($today)->locale('id')->format('d F Y')); ?></span></div>
                            <?php if(isset($holiday)): ?>
                            <div class="info-row"><span class="info-label">Status:</span><span class="info-value">📅 Libur: <?php echo e($holiday->note); ?></span></div>
                            <?php else: ?>
                            <div class="info-row"><span class="info-label">Status:</span><span class="info-value">📝 Absensi Aktif</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="tables-section">
                <div class="table-card">
                    <div class="table-header"><h2 class="table-title">Absensi Terbaru (<?php echo e($today); ?>)</h2></div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead><tr><th>Nama Siswa</th><th>Kelas</th><th>Status</th><th>Jam Masuk</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $statusConfig = [
                                        'hadir' => ['label' => 'Hadir', 'class' => 'success'],
                                        'sakit' => ['label' => 'Sakit', 'class' => 'warning'],
                                        'izin' => ['label' => 'Izin', 'class' => 'info'],
                                        'alpha' => ['label' => 'Alpa', 'class' => 'danger'],
                                        'belum_absen' => ['label' => 'Belum Absen', 'class' => 'secondary']
                                    ];
                                    $status = $statusConfig[$item['status']] ?? $statusConfig['belum_absen'];
                                ?>
                                <tr>
                                    <td><?php echo e($item['student']->name ?? '-'); ?></td>
                                    <td><?php echo e($item['class'] ?? '-'); ?></td>
                                    <td><span class="status-badge <?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span></td>
                                    <td><?php echo e($item['attendance_time'] ? \Carbon\Carbon::parse($item['attendance_time'])->format('H:i') : '-'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center py-4 text-gray-500">Belum ada data absensi</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
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
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; margin-bottom: 32px; }
.greeting-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
    margin-bottom: 32px; 
}
.greeting-subtitle { font-size: 16px; color: #64748b; }
.feature-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }
.feature-card { background: white; padding: 32px 24px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; text-align: center; transition: all 0.2s ease; }
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.feature-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.feature-icon.blue { background: #dbeafe; color: #3b82f6; }
.feature-icon.green { background: #dcfce7; color: #10b981; }
.feature-icon.orange { background: #fed7aa; color: #f97316; }
.feature-icon svg { width: 32px; height: 32px; }
.feature-title { font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
.feature-description { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
.feature-btn { display: inline-block; background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
.feature-btn:hover { background: #2563eb; }
.stats-section { margin-bottom: 32px; }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.stat-icon.balance { background: #dbeafe; color: #3b82f6; }
.stat-icon.income { background: #dcfce7; color: #10b981; }
.stat-icon.expense { background: #fee2e2; color: #ef4444; }
.stat-icon.remaining { background: #fef3c7; color: #f59e0b; }
.stat-icon.payment { background: #e0e7ff; color: #6366f1; }
.stat-icon.info { background: #f3f4f6; color: #6b7280; }
.stat-icon svg { width: 20px; height: 20px; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; }
.stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.stat-description { font-size: 14px; color: #64748b; }
.progress-container { margin-bottom: 16px; }
.progress-bar { width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); border-radius: 4px; transition: width 0.3s ease; }
.progress-text { text-align: center; font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 8px; }
.stat-details { display: flex; justify-content: space-between; gap: 16px; }
.detail-item { flex: 1; text-align: center; padding: 12px; background: #f8fafc; border-radius: 8px; }
.detail-label { font-size: 12px; color: #64748b; display: block; margin-bottom: 4px; }
.detail-value { font-size: 14px; font-weight: 600; color: #1e293b; }
.class-info { display: flex; flex-direction: column; gap: 12px; }
.info-row { display: flex; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; }
.info-label { font-size: 14px; color: #64748b; }
.info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
.tables-section { display: grid; grid-template-columns: 1fr; gap: 24px; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }
.table-title { font-size: 18px; font-weight: 600; color: #1e293b; }
.table-container { padding: 24px; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.data-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 14px; }
.data-table tr:hover td { background: #f8fafc; }
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.success { background: #dcfce7; color: #166534; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.info { background: #dbeafe; color: #1e40af; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }
.status-badge.secondary { background: #f3f4f6; color: #374151; }
.text-center { text-align: center; }
.py-4 { padding-top: 16px; padding-bottom: 16px; }
.text-gray-500 { color: #6b7280; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .tables-section { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .feature-cards { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: 1fr; gap: 16px; } }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/sekretaris/dashboard.blade.php ENDPATH**/ ?>
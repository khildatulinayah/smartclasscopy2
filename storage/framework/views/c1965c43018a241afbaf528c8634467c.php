<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="logo-img">
                <span class="logo-text">SMARTCLASS</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo e(route('admin.students')); ?>" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0zm1 3a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>CRUD Siswa</span>
            </a>
            <a href="<?php echo e(route('admin.monitor.absensi')); ?>" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Absensi</span>
            </a>
            <a href="<?php echo e(route('admin.monitor.kas')); ?>" class="nav-item active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Kas</span>
            </a>
            <a href="<?php echo e(route('admin.reports')); ?>" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Laporan</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-profile-mini">
                <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(auth()->user()->name)); ?>&background=3b82f6&color=fff" alt="User" class="user-avatar-mini">
                <div class="user-info-mini">
                    <div class="user-name-mini"><?php echo e(auth()->user()->name); ?></div>
                    <div class="user-role-mini"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="logout-form">
                <?php echo csrf_field(); ?>
                <button type="submit" class="logout-btn">
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Monitor Kas</h1>
                    <p class="greeting-subtitle">Pantau transaksi keuangan dan pembayaran mingguan (Read-Only)</p>
                </div>
            </section>

            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon income">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Total Pemasukan</div>
                        </div>
                        <div class="stat-value">Rp <?php echo e(number_format($totalIncome, 0, ',', '.')); ?></div>
                        <div class="stat-description">Bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 0h8m0 0v8m0-2v-2H9a2 2 0 00-2 2H6a2 2 0 00-2 2v2a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v2z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Total Pengeluaran</div>
                        </div>
                        <div class="stat-value">Rp <?php echo e(number_format($totalExpense, 0, ',', '.')); ?></div>
                        <div class="stat-description">Bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon balance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Saldo Kas</div>
                        </div>
                        <div class="stat-value">Rp <?php echo e(number_format($balance, 0, ',', '.')); ?></div>
                        <div class="stat-description">Saat ini</div>
                    </div>
                </div>
            </section>

            <!-- Weekly Payments Section -->
            <section class="table-header-section">
                <div class="table-header">
                    <h2 class="table-title">Pembayaran Mingguan</h2>
                    <div class="table-actions">
                        <div class="search-container">
                            <input type="text" id="searchPayment" placeholder="Cari siswa..." class="search-input">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <div class="table-card">
                <div class="table-container">
                    <table class="data-table" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Minggu 1</th>
                                <th>Minggu 2</th>
                                <th>Minggu 3</th>
                                <th>Minggu 4</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $weeklyPayments->groupBy('student_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $studentPayments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $student = $studentPayments->first()->student;
                                $week1Status = $studentPayments->where('week_number', 1)->first()->status ?? 'unpaid';
                                $week2Status = $studentPayments->where('week_number', 2)->first()->status ?? 'unpaid';
                                $week3Status = $studentPayments->where('week_number', 3)->first()->status ?? 'unpaid';
                                $week4Status = $studentPayments->where('week_number', 4)->first()->status ?? 'unpaid';
                            ?>
                            <tr data-name="<?php echo e(strtolower($student->name)); ?>">
                                <td class="font-semibold"><?php echo e($student->name); ?></td>
                                <td>
                                    <span class="status-badge <?php echo e($week1Status == 'paid' ? 'success' : 'warning'); ?>">
                                        <?php echo e($week1Status == 'paid' ? '✓' : '○'); ?> Rp 5.000
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($week2Status == 'paid' ? 'success' : 'warning'); ?>">
                                        <?php echo e($week2Status == 'paid' ? '✓' : '○'); ?> Rp 5.000
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($week3Status == 'paid' ? 'success' : 'warning'); ?>">
                                        <?php echo e($week3Status == 'paid' ? '✓' : '○'); ?> Rp 5.000
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($week4Status == 'paid' ? 'success' : 'warning'); ?>">
                                        <?php echo e($week4Status == 'paid' ? '✓' : '○'); ?> Rp 5.000
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($studentPayments->where('status', 'paid')->count() == 4 ? 'success' : 'warning'); ?>">
                                        <?php echo e($studentPayments->where('status', 'paid')->count()); ?>/4 Lunas
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <section class="table-header-section" style="margin-top: 32px;">
                <div class="table-header">
                    <h2 class="table-title">Transaksi Terbaru</h2>
                    <div class="table-actions">
                        <div class="search-container">
                            <input type="text" id="searchTransaction" placeholder="Cari transaksi..." class="search-input">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <div class="table-card">
                <div class="table-container">
                    <table class="data-table" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-description="<?php echo e(strtolower($transaction->description)); ?>" data-type="<?php echo e(strtolower($transaction->type)); ?>">
                                <td><?php echo e($transaction->date->format('d M Y')); ?></td>
                                <td><?php echo e($transaction->student->name ?? '-'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo e($transaction->type == 'income' ? 'success' : 'warning'); ?>">
                                        <?php echo e($transaction->type == 'income' ? 'Pemasukan' : 'Pengeluaran'); ?>

                                    </span>
                                </td>
                                <td class="font-semibold">Rp <?php echo e(number_format($transaction->amount, 0, ',', '.')); ?></td>
                                <td><?php echo e($transaction->description); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
* { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
}

.dashboard-layout { 
    display: flex; 
    height: 100vh; 
    background: #f8fafc; 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
}

/* Sidebar Styles */
.sidebar { 
    width: 280px; 
    background: white; 
    border-right: 1px solid #e2e8f0; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    display: flex; 
    flex-direction: column; 
}

.sidebar-header { 
    padding: 24px 20px; 
    border-bottom: 1px solid #e2e8f0; 
}

.logo { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}

.logo-img { 
    width: 40px; 
    height: 40px; 
    border-radius: 8px; 
    object-fit: cover; 
}

.logo-text { 
    font-size: 20px; 
    font-weight: 700; 
    color: #1e293b; 
}

.sidebar-nav { 
    flex: 1; 
    padding: 16px 0; 
}

.nav-item { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    padding: 12px 20px; 
    color: #64748b; 
    text-decoration: none; 
    transition: all 0.2s ease; 
    border-radius: 0 8px 8px 0; 
    margin: 0 12px; 
}

.nav-item:hover { 
    background: #f8fafc; 
    color: #3b82f6; 
}

.nav-item.active { 
    background: #eff6ff; 
    color: #3b82f6; 
    font-weight: 600; 
}

.nav-icon { 
    width: 20px; 
    height: 20px; 
}

.sidebar-footer { 
    padding: 16px 20px; 
    border-top: 1px solid #e2e8f0; 
}

.user-profile-mini { 
    display: flex; 
    align-items: center; 
    gap: 10px; 
    margin-bottom: 12px; 
}

.user-avatar-mini { 
    width: 32px; 
    height: 32px; 
    border-radius: 6px; 
    object-fit: cover; 
}

.user-name-mini { 
    font-size: 13px; 
    font-weight: 600; 
    color: #1e293b; 
}

.user-role-mini { 
    font-size: 11px; 
    color: #64748b; 
}

.logout-form { 
    display: block; 
}

.logout-btn { 
    width: 100%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 8px; 
    background: #fee2e2; 
    color: #dc2626; 
    border: none; 
    padding: 8px 12px; 
    border-radius: 8px; 
    font-size: 13px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.logout-btn:hover { 
    background: #fecaca; 
}

.logout-icon { 
    width: 16px; 
    height: 16px; 
}

/* Main Content */
.main-area { 
    flex: 1; 
    display: flex; 
    flex-direction: column; 
    overflow: hidden; 
}

.main-content { 
    flex: 1; 
    padding: 32px; 
    overflow-y: auto; 
}

/* Greeting Section */
.greeting-section { 
    margin-bottom: 32px; 
}

.greeting-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.greeting-title { 
    font-size: 32px; 
    font-weight: 700; 
    color: #1e293b; 
    margin-bottom: 8px; 
}

.greeting-subtitle { 
    font-size: 16px; 
    color: #64748b; 
}

/* Stats Section */
.stats-section { 
    margin-bottom: 32px; 
}

.stats-grid { 
    display: grid; 
    grid-template-columns: repeat(3, 1fr); 
    gap: 24px; 
}

.stat-card { 
    background: white; 
    border-radius: 16px; 
    padding: 24px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.stat-header { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    margin-bottom: 20px; 
}

.stat-icon { 
    width: 40px; 
    height: 40px; 
    border-radius: 10px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
}

.stat-icon.income { 
    background: #dcfce7; 
    color: #10b981; 
}

.stat-icon.expense { 
    background: #fee2e2; 
    color: #ef4444; 
}

.stat-icon.balance { 
    background: #dbeafe; 
    color: #3b82f6; 
}

.stat-icon svg { 
    width: 20px; 
    height: 20px; 
}

.stat-title { 
    font-size: 16px; 
    font-weight: 600; 
    color: #1e293b; 
}

.stat-value { 
    font-size: 28px; 
    font-weight: 700; 
    color: #1e293b; 
    margin-bottom: 8px; 
}

.stat-description { 
    font-size: 14px; 
    color: #64748b; 
}

/* Table Section */
.table-header-section { 
    margin-bottom: 24px; 
}

.table-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    background: white; 
    padding: 24px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.table-title { 
    font-size: 18px; 
    font-weight: 600; 
    color: #1e293b; 
    margin: 0; 
}

.table-actions { 
    display: flex; 
    gap: 16px; 
    align-items: center; 
}

.search-container { 
    position: relative; 
}

.search-input { 
    padding: 12px 40px 12px 16px; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    font-size: 14px; 
    width: 300px; 
    transition: all 0.2s; 
}

.search-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1); 
}

.search-icon { 
    position: absolute; 
    right: 12px; 
    top: 50%; 
    transform: translateY(-50%); 
    width: 20px; 
    height: 20px; 
    color: #64748b; 
}

/* Table Styles */
.table-card { 
    background: white; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
    overflow: hidden; 
}

.table-container { 
    padding: 24px; 
}

.data-table { 
    width: 100%; 
    border-collapse: collapse; 
}

.data-table th { 
    background: #f8fafc; 
    color: #475569; 
    padding: 12px; 
    text-align: left; 
    font-weight: 600; 
    font-size: 12px; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    border-bottom: 1px solid #e2e8f0; 
}

.data-table td { 
    padding: 16px 12px; 
    border-bottom: 1px solid #f1f5f9; 
    color: #334155; 
    font-size: 14px; 
}

.data-table tr:hover td { 
    background: #f8fafc; 
}

.font-semibold { 
    font-weight: 600; 
}

/* Status Badges */
.status-badge { 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}

.status-badge.success { 
    background: #dcfce7; 
    color: #166534; 
}

.status-badge.warning { 
    background: #fef3c7; 
    color: #92400e; 
}

/* Responsive */
@media (max-width: 1200px) { 
    .stats-grid { 
        grid-template-columns: 1fr; 
    } 
}

@media (max-width: 768px) { 
    .sidebar { 
        width: 260px; 
    } 
    .main-content { 
        padding: 20px; 
    } 
    .table-header { 
        flex-direction: column; 
        gap: 16px; 
        align-items: stretch; 
    } 
    .table-actions { 
        flex-direction: column; 
    } 
    .search-input { 
        width: 100%; 
    } 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality for payments table
    const searchPaymentInput = document.getElementById('searchPayment');
    const paymentsRows = document.querySelectorAll('#paymentsTable tbody tr');
    
    if (searchPaymentInput) {
        searchPaymentInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            paymentsRows.forEach(row => {
                const name = row.dataset.name;
                if (name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Search functionality for transactions table
    const searchTransactionInput = document.getElementById('searchTransaction');
    const transactionsRows = document.querySelectorAll('#transactionsTable tbody tr');
    
    if (searchTransactionInput) {
        searchTransactionInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            transactionsRows.forEach(row => {
                const description = row.dataset.description;
                const type = row.dataset.type;
                if (description.includes(query) || type.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/admin/monitor_kas.blade.php ENDPATH**/ ?>
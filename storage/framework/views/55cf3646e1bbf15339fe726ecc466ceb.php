<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Monitor Pembayaran Mingguan</h1>
                    <p class="greeting-subtitle">Pantau pembayaran kas mingguan siswa (Read-Only)</p>
                </div>
            </section>

            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon income">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Total Sudah Bayar</div>
                        </div>
                        <div class="stat-value"><?php echo e($weeklyPayments->where('status', 'paid')->count()); ?> Pembayaran</div>
                        <div class="stat-description"><?php echo e($monthName); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Total Belum Bayar</div>
                        </div>
                        <div class="stat-value"><?php echo e($weeklyPayments->where('status', 'unpaid')->count()); ?> Pembayaran</div>
                        <div class="stat-description"><?php echo e($monthName); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon balance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Nominal Kas</div>
                        </div>
                        <div class="stat-value">Rp <?php echo e(number_format($currentKasNominal, 0, ',', '.')); ?></div>
                        <div class="stat-description">Per minggu</div>
                    </div>
                </div>
            </section>

            <!-- Weekly Payments Section -->
            <section class="table-header-section">
                <div class="table-header">
                    <div class="table-title-section">
                        <h2 class="table-title">Pembayaran Mingguan</h2>
                        <div class="month-navigation">
                            <a href="<?php echo e(route('admin.monitor.pembayaran', ['month' => $prevDate->month, 'year' => $prevDate->year])); ?>" class="nav-btn">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Prev
                            </a>
                            <span class="current-month"><?php echo e($monthName); ?></span>
                            <a href="<?php echo e(route('admin.monitor.pembayaran', ['month' => $nextDate->month, 'year' => $nextDate->year])); ?>" class="nav-btn">
                                Next
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
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
                                <th>No</th>
                                <th>Siswa</th>
                                <?php $__currentLoopData = $wednesdayDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th>
                                        <?php echo e($date->format('d M')); ?><br>
                                        <small style="font-weight: normal; text-transform: none;">Rabu</small><br>
                                        <small style="font-weight: normal; text-transform: none; color: #3b82f6;">Minggu <?php echo e($index + 1); ?></small>
                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
    // Precompute adjustments for O(1) lookup in the table (read-only)
    $adjustmentByStudentWeek = $adjustmentByStudentWeek ?? [];
?>

<?php $__currentLoopData = $weeklyPayments->groupBy('student_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $studentPayments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php
                                $student = $studentPayments->first()->student;
                                $totalWeeks = count($wednesdayDates);
                                $index = $loop->index;
                            ?>
                            <tr data-name="<?php echo e(strtolower($student->name)); ?>">
                                <td><?php echo e($index + 1); ?></td>
                                <td class="font-semibold"><?php echo e($student->name); ?></td>


                                    <?php $__currentLoopData = $wednesdayDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekIndex => $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $weekNumber = $weekIndex + 1;
                                            $payment = $studentPayments->where('week_number', $weekNumber)->first();
                                            $status = $payment ? $payment->status : 'unpaid';
                                            $amount = $payment ? $payment->amount : $currentKasNominal;

                                            // Read-only kas adjustment badge
                                            $adj = null;
                                            if (!empty($adjustmentByStudentWeek) && $payment) {
                                                $key = $payment->student_id . ':' . $payment->week_number;
                                                $adj = $adjustmentByStudentWeek[$key] ?? null;
                                            }
                                        ?>

                                        <td>
                                            <div class="cell-wrap">
                                                <span class="status-badge <?php echo e($status == 'paid' ? 'success' : 'warning'); ?>">
                                                    <?php echo e($status == 'paid' ? '✓' : '○'); ?> Rp <?php echo e(number_format($amount, 0, ',', '.')); ?>

                                                </span>

                                                <?php if($adj): ?>
                                                    <?php
                                                        $adjType = $adj->adjustment_type_label ?? ($adj->adjustment_type === 'shortage' ? 'Kurang Bayar' : ($adj->adjustment_type === 'overpayment' ? 'Kelebihan Bayar' : 'Tidak Diketahui'));
                                                        $adjStatus = $adj->status_label ?? $adj->status;
                                                        $adjAmountAbs = isset($adj->adjustment_amount) ? abs((float)$adj->adjustment_amount) : null;
                                                        $adjAmountStr = $adjAmountAbs !== null ? ('Rp ' . number_format($adjAmountAbs, 0, ',', '.')) : '-';
                                                        $handling = $adj->handling_method_label ?? ($adj->handling_method ?? '');
                                                        $tooltip = "{$adjType} | {$adjStatus}\nSelisih: {$adjAmountStr}\nMetode: {$handling}";
                                                    ?>

                                                    <span
                                                        class="adj-badge <?php echo e($adj->adjustment_type === 'shortage' ? 'adj-shortage' : 'adj-overpayment'); ?>"
                                                        title="<?php echo e($tooltip); ?>"
                                                    >
                                                        <?php echo e($adj->adjustment_type === 'shortage' ? 'SHORT' : 'OVER'); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <td>
                                        <span class="status-badge <?php echo e($studentPayments->where('status', 'paid')->count() == $totalWeeks ? 'success' : 'warning'); ?>">
                                            <?php echo e($studentPayments->where('status', 'paid')->count()); ?>/<?php echo e($totalWeeks); ?> Lunas
                                        </span>
                                    </td>
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

.table-title-section { 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
}

.table-title { 
    font-size: 18px; 
    font-weight: 600; 
    color: #1e293b; 
    margin: 0; 
}

.month-navigation { 
    display: flex; 
    align-items: center; 
    gap: 16px; 
}

.nav-btn { 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    padding: 8px 12px; 
    background: #f8fafc; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    color: #64748b; 
    text-decoration: none; 
    font-size: 14px; 
    font-weight: 500; 
    transition: all 0.2s ease; 
}

.nav-btn:hover { 
    background: #eff6ff; 
    color: #3b82f6; 
    border-color: #3b82f6; 
}

.current-month { 
    font-size: 16px; 
    font-weight: 600; 
    color: #1e293b; 
    min-width: 150px; 
    text-align: center; 
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

/* Date columns styling */
.data-table th:not(:first-child):not(:last-child) { 
    text-align: center; 
    min-width: 120px; 
    line-height: 1.1;
    padding: 16px 8px;
}

.data-table th small {
    color: #64748b;
    font-size: 10px;
}

.data-table th small:nth-of-type(2) {
    color: #3b82f6;
    font-weight: 600;
}

.data-table td:not(:first-child):not(:last-child) { 
    text-align: center; 
    min-width: 120px; 
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.status-badge.success { 
    background: #dcfce7; 
    color: #166534; 
}

.status-badge.warning { 
    background: #fef3c7; 
    color: #92400e; 
}

/* Adjustment badge (read-only) */
.cell-wrap{
    display:flex;
    flex-direction:column;
    gap:6px;
    align-items:center;
}

.adj-badge{
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .2px;
}

.adj-shortage{
    background:#fef3c7;
    color:#92400e;
    border:1px solid rgba(146,64,14,.25);
}

.adj-overpayment{
    background:#e0e7ff;
    color:#3730a3;
    border:1px solid rgba(55,48,163,.25);
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
    .month-navigation { 
        justify-content: space-between; 
        gap: 8px; 
    } 
    .nav-btn { 
        padding: 6px 8px; 
        font-size: 12px; 
    } 
    .current-month { 
        font-size: 14px; 
        min-width: 100px; 
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
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/admin/monitor-pembayaran.blade.php ENDPATH**/ ?>
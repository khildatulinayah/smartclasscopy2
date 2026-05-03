<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.siswa-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content Area -->
    <div class="main-area">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <div class="topbar-right">
                <button class="notification-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="notification-badge">3</span>
                </button>
                <div class="user-profile">
                    <img src="https://picsum.photos/seed/student/40/40.jpg" alt="User" class="user-avatar">
                    <div class="user-info">
                        <div class="user-name"><?php echo e(auth()->user()->name); ?></div>
                        <div class="user-role">Siswa</div>
                    </div>
                    <button class="user-menu-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <section class="page-header">
                <div class="page-title">
                    <h1>Pembayaran Kas</h1>
                    <p>Kelola dan pantau pembayaran kas mingguan Anda</p>
                </div>
                <div class="page-actions">
                    <div class="month-navigation">
                        <a href="<?php echo e(route('siswa.pembayaran.month', [$prevMonth->month, $prevMonth->year])); ?>" class="nav-btn prev-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Prev</span>
                        </a>
                        
                        <div class="current-month">
                            <span class="month-label"><?php echo e($currentDate->translatedFormat('F Y')); ?></span>
                        </div>
                        
                        <a href="<?php echo e(route('siswa.pembayaran.month', [$nextMonth->month, $nextMonth->year])); ?>" class="nav-btn next-btn">
                            <span>Next</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <button class="payment-history-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Pembayaran
                    </button>
                </div>
            </section>

            <!-- Payment Summary -->
            <section class="payment-summary">
                <div class="summary-card">
                    <div class="summary-header">
                        <h2>Ringkasan Pembayaran <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></h2>
                        <div class="summary-period">
                            <span class="period-label">Periode:</span>
                            <span class="period-value">Minggu 1-4</span>
                        </div>
                    </div>
                    
                    <div class="summary-stats">
                        <div class="progress-circle">
                            <svg class="progress-ring" width="120" height="120">
                                <circle class="progress-ring__background" stroke="#e2e8f0" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
                                <circle class="progress-ring__progress" stroke="#3b82f6" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"
                                    stroke-dasharray="326.73" stroke-dashoffset="<?php echo e($totalWeeks > 0 ? 326.73 - ($paidWeeks / $totalWeeks * 326.73) : 326.73); ?>"/>
                            </svg>
                            <div class="progress-text"><?php echo e($totalWeeks > 0 ? round(($paidWeeks / $totalWeeks) * 100, 0) : 0); ?>%</div>
                        </div>
                        
                        <div class="summary-details">
                            <div class="detail-row">
                                <span class="detail-label">Total Minggu:</span>
                                <span class="detail-value"><?php echo e($totalWeeks ?? 4); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Sudah Dibayar:</span>
                                <span class="detail-value paid"><?php echo e($paidWeeks ?? 0); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Belum Dibayar:</span>
                                <span class="detail-value unpaid"><?php echo e(($totalWeeks - $paidWeeks) ?? 4); ?></span>
                            </div>
                            <div class="detail-row total">
                                <span class="detail-label">Total Pembayaran:</span>
                                <span class="detail-value">Rp <?php echo e(number_format($kasSudahBayar ?? 0, 0, ',', '.')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Weekly Payment Details -->
            <section class="weekly-payments">
                <div class="section-header">
                    <h2>Detail Pembayaran Mingguan</h2>
                    <div class="month-selector">
                        <select class="month-select" id="monthSelect">
                            <option value=""><?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="payments-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $weeklyPayments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="payment-card <?php echo e($payment->status == 'paid' ? 'paid' : 'unpaid'); ?>">
                            <div class="payment-header">
                                <h3>Minggu <?php echo e($payment->week_number); ?></h3>
                                <span class="payment-status <?php echo e($payment->status); ?>">
                                    <?php echo e($payment->status == 'paid' ? 'Lunas' : 'Belum Lunas'); ?>

                                </span>
                            </div>
                            
                            <div class="payment-details">
                                <div class="payment-amount">
                                    <span class="amount-label">Jumlah:</span>
                                    <span class="amount-value">Rp <?php echo e(number_format($payment->amount ?? 15000, 0, ',', '.')); ?></span>
                                </div>
                                
                                <?php if($payment->status == 'paid'): ?>
                                    <div class="payment-date">
                                        <span class="date-label">Tanggal Bayar:</span>
                                        <span class="date-value"><?php echo e(\Carbon\Carbon::parse($payment->paid_date)->format('d/m/Y')); ?></span>
                                    </div>
                                    
                                    <?php if($payment->payment_method): ?>
                                        <div class="payment-method">
                                            <span class="method-label">Metode:</span>
                                            <span class="method-value"><?php echo e(ucfirst($payment->payment_method)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            
                                                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="no-payments">
                            <div class="no-payments-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3>Belum Ada Data Pembayaran</h3>
                            <p>Data pembayaran untuk bulan ini belum tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Payment History -->
            <section class="payment-history">
                <div class="section-header">
                    <h2>Riwayat Pembayaran</h2>
                    <button class="export-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                </div>
                
                <div class="history-table">
                    <table class="payment-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Minggu</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $paymentHistory ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e(\Carbon\Carbon::parse($history->date)->format('d/m/Y')); ?></td>
                                    <td>Minggu <?php echo e(ceil(\Carbon\Carbon::parse($history->date)->day / 7)); ?></td>
                                    <td>Rp <?php echo e(number_format($history->amount, 0, ',', '.')); ?></td>
                                    <td><?php echo e(ucfirst($history->payment_method ?? 'cash')); ?></td>
                                    <td>
                                        <span class="status-badge paid">
                                            Lunas
                                        </span>
                                    </td>
                                    <td><?php echo e($history->description ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="no-data">Belum ada riwayat pembayaran</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Konfirmasi Pembayaran</h3>
            <button class="close-btn" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="payment-info">
                <div class="info-row">
                    <span class="info-label">Minggu:</span>
                    <span class="info-value" id="modalWeek">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah:</span>
                    <span class="info-value" id="modalAmount">-</span>
                </div>
            </div>
            
            <form class="payment-form">
                <div class="form-group">
                    <label for="paymentMethod">Metode Pembayaran</label>
                    <select id="paymentMethod" name="payment_method" required>
                        <option value="">Pilih Metode</option>
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="digital">E-Wallet</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="paymentDate">Tanggal Pembayaran</label>
                    <input type="date" id="paymentDate" name="payment_date" required>
                </div>
                
                <div class="form-group">
                    <label for="paymentNotes">Keterangan (Opsional)</label>
                    <textarea id="paymentNotes" name="notes" rows="3" placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="cancel-btn" onclick="closePaymentModal()">Batal</button>
            <button class="confirm-btn" onclick="confirmPayment()">Konfirmasi Pembayaran</button>
        </div>
    </div>
</div>

<style>
/* ===== BASE STYLES ===== */
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

/* ===== SIDEBAR ===== */
.sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.sidebar-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; }
.user-profile-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.user-avatar-mini { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }
.user-name-mini { font-size: 13px; font-weight: 600; color: #1e293b; }
.user-role-mini { font-size: 11px; color: #64748b; }
.logout-form { display: block; }
.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.logout-btn:hover { background: #fecaca; }
.logout-icon { width: 16px; height: 16px; }

/* ===== MAIN AREA ===== */
.main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===== TOPBAR ===== */
.topbar {
    height: 72px;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
}

.topbar-left {
    display: flex;
    align-items: center;
}

.menu-toggle {
    background: none;
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}

.menu-toggle:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.notification-btn {
    position: relative;
    background: none;
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}

.notification-btn:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.notification-btn svg {
    width: 20px;
    height: 20px;
}

.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.user-profile:hover {
    background: #f8fafc;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.user-role {
    font-size: 12px;
    color: #64748b;
}

.user-menu-btn {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    color: #64748b;
}

.user-menu-btn:hover {
    color: #3b82f6;
}

/* ===== MAIN CONTENT ===== */
.main-content {
    flex: 1;
    padding: 32px;
    overflow-y: auto;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}

.page-title p {
    font-size: 16px;
    color: #64748b;
}

.page-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.month-navigation {
    display: flex;
    align-items: center;
    gap: 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 16px;
}

.nav-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    background: #f8fafc;
    color: #64748b;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.nav-btn:hover {
    background: #e2e8f0;
    color: #3b82f6;
}

.nav-btn svg {
    width: 16px;
    height: 16px;
}

.current-month {
    padding: 0 16px;
    border-left: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
}

.month-label {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    min-width: 120px;
    text-align: center;
}

.payment-history-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #3b82f6;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-history-btn:hover {
    background: #2563eb;
}

/* Payment Summary */
.payment-summary {
    margin-bottom: 32px;
}

.summary-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.summary-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
}

.summary-period {
    display: flex;
    align-items: center;
    gap: 8px;
}

.period-label {
    font-size: 14px;
    color: #64748b;
}

.period-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.summary-stats {
    display: flex;
    gap: 48px;
    align-items: center;
}

.progress-circle {
    position: relative;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring__background {
    stroke: #e2e8f0;
}

.progress-ring__progress {
    stroke: #3b82f6;
    transition: stroke-dashoffset 0.35s;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.summary-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 8px;
}

.detail-row.total {
    background: #eff6ff;
    border: 1px solid #3b82f6;
}

.detail-label {
    font-size: 14px;
    color: #64748b;
}

.detail-value {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.detail-value.paid {
    color: #10b981;
}

.detail-value.unpaid {
    color: #ef4444;
}

/* Weekly Payments */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
}

.month-select {
    padding: 10px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    background: white;
    cursor: pointer;
}

.payments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.payment-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.payment-card.paid {
    border-left: 4px solid #10b981;
}

.payment-card.unpaid {
    border-left: 4px solid #ef4444;
}

.payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.payment-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.payment-status {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.payment-status.paid {
    background: #dcfce7;
    color: #10b981;
}

.payment-status.unpaid {
    background: #fee2e2;
    color: #ef4444;
}

.payment-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.payment-amount,
.payment-date,
.payment-method,
.payment-due {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.amount-label,
.date-label,
.method-label,
.due-label {
    font-size: 14px;
    color: #64748b;
}

.amount-value,
.date-value,
.method-value,
.due-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.payment-actions {
    display: flex;
    justify-content: center;
}

.pay-btn {
    width: 100%;
    background: #3b82f6;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pay-btn:hover {
    background: #2563eb;
}

.no-payments {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px;
    color: #64748b;
}

.no-payments-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-payments-icon svg {
    width: 32px;
    height: 32px;
    color: #64748b;
}

.no-payments h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.no-payments p {
    font-size: 14px;
}

/* Payment History */
.export-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: white;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.export-btn:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.export-btn svg {
    width: 16px;
    height: 16px;
}

.history-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.payment-table {
    width: 100%;
    border-collapse: collapse;
}

.payment-table th {
    background: #f8fafc;
    padding: 16px;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.payment-table td {
    padding: 16px;
    font-size: 14px;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
}

.payment-table tr:last-child td {
    border-bottom: none;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.paid,
.status-badge.lunas {
    background: #dcfce7;
    color: #10b981;
}

.status-badge.pending {
    background: #fef3c7;
    color: #f59e0b;
}

.no-data {
    text-align: center;
    color: #64748b;
    font-style: italic;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #64748b;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.close-btn:hover {
    background: #f8fafc;
}

.modal-body {
    padding: 24px;
}

.payment-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 24px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 8px;
}

.info-label {
    font-size: 14px;
    color: #64748b;
}

.info-value {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.payment-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 24px;
    border-top: 1px solid #e2e8f0;
}

.cancel-btn {
    padding: 12px 20px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cancel-btn:hover {
    background: #f8fafc;
}

.confirm-btn {
    padding: 12px 20px;
    border: none;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.confirm-btn:hover {
    background: #2563eb;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    .summary-stats {
        flex-direction: column;
        gap: 24px;
        text-align: center;
        align-items: center;
    }
    
    .payments-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 16px !important;
    }
    
    @media (max-width: 480px) {
        .payments-grid {
            grid-template-columns: 1fr !important;
        }
    }
    
    .history-table {
        overflow-x: auto;
    }
    
    .payment-table {
        min-width: 600px;
    }
}
</style>

<script>
// Payment Modal Functions
function showPaymentModal(week, amount) {
    document.getElementById('modalWeek').textContent = 'Minggu ' + week;
    document.getElementById('modalAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('paymentModal').style.display = 'block';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.querySelector('.payment-form').reset();
}

function confirmPayment() {
    const form = document.querySelector('.payment-form');
    const formData = new FormData(form);
    
    // Validate form
    if (!formData.get('payment_method') || !formData.get('payment_date')) {
        showWarningToast('Mohon lengkapi semua field yang wajib diisi');
        return;
    }
    
    // Here you would normally send the data to the server
    console.log('Payment confirmed:', Object.fromEntries(formData));
    
    // Show success message
    showSuccessToast('Pembayaran berhasil dikonfirmasi!');
    closePaymentModal();
    
    // Reload page to show updated payment status
    window.location.reload();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target == modal) {
        closePaymentModal();
    }
}

// Initialize date input with today's date
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('paymentDate');
    if (dateInput) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/siswa/pembayaran.blade.php ENDPATH**/ ?>
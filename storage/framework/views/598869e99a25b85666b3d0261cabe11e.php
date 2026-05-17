<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Monitor Keuangan</h1>
                    <p class="greeting-subtitle">Pantau transaksi keuangan dan arus kas (Read-Only)</p>
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
                        <div class="stat-description"><?php echo e($monthName); ?></div>
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
                        <div class="stat-description"><?php echo e($monthName); ?></div>
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

            <!-- Recent Transactions -->
            <section class="table-header-section">
                <div class="table-header">
                    <div class="table-title-section">
                        <h2 class="table-title">Transaksi Terbaru</h2>
                        <div class="month-navigation">
                            <a href="<?php echo e(route('admin.monitor.keuangan', ['month' => $prevDate->month, 'year' => $prevDate->year])); ?>" class="nav-btn">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Prev
                            </a>
                            <span class="current-month"><?php echo e($monthName); ?></span>
                            <a href="<?php echo e(route('admin.monitor.keuangan', ['month' => $nextDate->month, 'year' => $nextDate->year])); ?>" class="nav-btn">
                                Next
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="table-actions">
                        <div class="action-buttons">
                            <div class="filter-buttons">
                                <button onclick="filterTransactions('all')" class="filter-btn active" data-type="all">
                                    Semua
                                </button>
                                <button onclick="filterTransactions('income')" class="filter-btn" data-type="income">
                                    Pemasukan
                                </button>
                                <button onclick="filterTransactions('expense')" class="filter-btn" data-type="expense">
                                    Pengeluaran
                                </button>
                            </div>
                        </div>
                        <div class="search-container">
                            <input type="text" id="searchTransaction" placeholder="Cari transaksi..." class="search-input">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <div id="transactionsContainer">
                <!-- Normal Table View -->
                <div class="table-card" id="normalView">
                    <div class="table-container">
                        <table class="data-table" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Siswa</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-description="<?php echo e(strtolower($transaction->description)); ?>" data-type="<?php echo e(strtolower($transaction->type)); ?>" data-id="<?php echo e($transaction->id); ?>" data-receipt-path="<?php echo e($transaction->receipt_path ?? ''); ?>">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($transaction->date->format('d M Y')); ?></td>
                                    <td><?php echo e($transaction->student->name ?? $transaction->creator->name ?? '-'); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo e($transaction->type == 'income' ? 'success' : 'warning'); ?>">
                                            <?php echo e($transaction->type == 'income' ? 'Pemasukan' : 'Pengeluaran'); ?>

                                        </span>
                                    </td>
                                    <td class="font-semibold">Rp <?php echo e(number_format($transaction->amount, 0, ',', '.')); ?></td>
                                    <td><?php echo e($transaction->description); ?></td>
                                    <td>
                                        <button class="detail-btn" onclick="showTransactionDetail(<?php echo e($transaction->id); ?>)" title="Lihat detail transaksi">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
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

.action-buttons { 
    display: flex; 
    gap: 16px; 
    align-items: center; 
}

.filter-buttons { 
    display: flex; 
    gap: 8px; 
}

.filter-btn { 
    padding: 8px 16px; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    background: white; 
    color: #64748b; 
    font-size: 14px; 
    font-weight: 500; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.filter-btn:hover { 
    background: #f8fafc; 
    border-color: #3b82f6; 
    color: #3b82f6; 
}

.filter-btn.active { 
    background: #3b82f6; 
    border-color: #3b82f6; 
    color: white; 
}

.filter-btn[data-type="income"].active { 
    background: #10b981; 
    border-color: #10b981; 
}

.filter-btn[data-type="expense"].active { 
    background: #ef4444; 
    border-color: #ef4444; 
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


.detail-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.detail-btn:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.detail-btn svg {
    flex-shrink: 0;
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
    const searchTransactionInput = document.getElementById('searchTransaction');
    const transactionsRows = document.querySelectorAll('#transactionsTable tbody tr');
    const transactionsData = [];
    
    // Collect transaction data
    transactionsRows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        transactionsData.push({
            element: row,
            index: index + 1,
            date: cells[1].textContent.trim(),
            student: cells[2].textContent.trim(),
            type: row.dataset.type,
            amount: cells[4].textContent.trim(),
            description: cells[5].textContent.trim(),
            descriptionLower: row.dataset.description,
            typeLower: row.dataset.type,
            receiptPath: row.dataset.receiptPath || null
        });
    });
    
    // Filter functionality
    window.filterTransactions = function(type) {
        // Update button states
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${type}"]`).classList.add('active');
        
        applyFiltersAndSearch();
    };
    
        
    // Apply filters and search
    function applyFiltersAndSearch() {
        const query = searchTransactionInput ? searchTransactionInput.value.toLowerCase() : '';
        const activeFilter = document.querySelector('.filter-btn.active').dataset.type;
        
        transactionsRows.forEach(row => {
            const description = row.dataset.description;
            const type = row.dataset.type;
            const matchesSearch = !query || description.includes(query) || type.includes(query);
            const matchesFilter = activeFilter === 'all' || type === activeFilter;
            
            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Search functionality
    if (searchTransactionInput) {
        searchTransactionInput.addEventListener('input', applyFiltersAndSearch);
    }
    
    // Show transaction detail
    window.showTransactionDetail = function(transactionId) {
        // Find transaction data
        const transaction = transactionsData.find(t => t.element.dataset.id == transactionId);
        if (!transaction) return;
        
        const modal = document.getElementById('transactionDetailModal');
        const content = document.getElementById('transactionDetailContent');
        
        const isIncome = transaction.typeLower === 'income';
        const formattedDate = new Date(transaction.date).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Parse description untuk mengambil informasi minggu dan bulan
        const parsedInfo = parseTransactionDescription(transaction.description);
        
        // If no month/year found in description, use transaction date as fallback
        if (!parsedInfo.month && !parsedInfo.year) {
            const transactionDate = new Date(transaction.date);
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            parsedInfo.month = monthNames[transactionDate.getMonth()];
            parsedInfo.year = transactionDate.getFullYear().toString();
            parsedInfo.monthNumber = transactionDate.getMonth() + 1;
        }
        
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Header Info -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 rounded-full text-sm font-bold bg-opacity-10 border ${isIncome ? 'bg-green text-green-800 border-green-200' : 'bg-red text-red-800 border-red-200'}">
                            ${isIncome ? 'PEMASUKAN' : 'PENGELUARAN'}
                        </div>
                        <div class="text-3xl font-bold ${isIncome ? 'text-green-600' : 'text-red-600'}">
                            ${isIncome ? '+' : '-'} Rp ${Number(transaction.amount.replace(/[^\d]/g, '')).toLocaleString('id-ID')}
                        </div>
                    </div>
                </div>
                
                <!-- Transaction Details -->
                <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal</label>
                            <p class="text-lg font-semibold text-gray-900">${formattedDate}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID Transaksi</label>
                            <p class="text-lg font-semibold text-gray-900">#${transactionId}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Keterangan</label>
                        <p class="text-lg font-semibold text-gray-900">${transaction.description}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Siswa/Creator</label>
                        <p class="text-lg font-semibold text-gray-900">${transaction.student}</p>
                    </div>
                    
                    ${parsedInfo.week ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Minggu</label>
                        <p class="text-lg font-semibold text-gray-900">${parsedInfo.week}</p>
                    </div>
                    ` : ''}
                    
                    ${parsedInfo.month ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Bulan</label>
                        <p class="text-lg font-semibold text-gray-900">${parsedInfo.month}</p>
                    </div>
                    ` : ''}
                    
                    ${parsedInfo.year ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tahun</label>
                        <p class="text-lg font-semibold text-gray-900">${parsedInfo.year}</p>
                    </div>
                    ` : ''}
                    
                    ${transaction.receiptPath ? `
                    <div>
                        <label class="text-sm font-medium text-gray-500">Bukti Transaksi</label>
                        <div class="mt-2">
                            <img src="/${transaction.receiptPath}" alt="Bukti Transaksi" 
                                 class="max-w-xs rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow"
                                 onclick="window.open('/${transaction.receiptPath}', '_blank')">
                        <p class="text-xs text-gray-500 mt-1">Klik gambar untuk memperbesar</p>
                    </div>
                </div>
                ` : ''}
                </div>
                
                <!-- Additional Info for Special Transactions -->
                ${parsedInfo.isWeeklyPayment ? `
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-green-800 mb-2">💰 Informasi Pembayaran Mingguan</h4>
                    <p class="text-sm text-green-700">Transaksi ini merupakan pembayaran kas mingguan untuk periode ${parsedInfo.month} ${parsedInfo.year}.</p>
                    ${parsedInfo.week ? `<p class="text-xs text-green-600 mt-1">Pembayaran untuk ${parsedInfo.week}</p>` : ''}
                </div>
                ` : ''}
                
                ${parsedInfo.isArrearsPayment ? `
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-blue-800 mb-2">📋 Informasi Pelunasan Tunggakan</h4>
                    <p class="text-sm text-blue-700">Transaksi ini merupakan pelunasan tunggakan kas untuk periode ${parsedInfo.month} ${parsedInfo.year}.</p>
                    ${parsedInfo.week ? `<p class="text-xs text-blue-600 mt-1">Mencakup ${parsedInfo.week}</p>` : ''}
                    <p class="text-xs text-blue-500 mt-2">*Bulan diambil dari tanggal transaksi</p>
                </div>
                ` : ''}
            </div>
        `;
        
        modal.classList.remove('hidden');
    };
    
        
    // Initialize
    applyFiltersAndSearch();
});
</script>

<!-- Transaction Detail Modal -->
<div id="transactionDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Detail Transaksi</h2>
                <button onclick="closeTransactionDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div id="transactionDetailContent" class="p-6">
            <!-- Dynamic content will be loaded here -->
        </div>
    </div>
</div>

<script>
// Function to parse transaction description
function parseTransactionDescription(description) {
    const info = {
        week: null,
        month: null,
        year: null,
        monthNumber: null,
        isWeeklyPayment: false,
        isArrearsPayment: false
    };
    
    // Check for weekly payment patterns
    const weeklyPatterns = [
        /pembayaran\s+kas\s+minggu\s+ke-(\d+)/i,
        /kas\s+minggu\s+ke-(\d+)/i,
        /minggu\s+ke-(\d+)/i
    ];
    
    for (const pattern of weeklyPatterns) {
        const match = description.match(pattern);
        if (match) {
            info.week = `Minggu ke-${match[1]}`;
            info.isWeeklyPayment = true;
            break;
        }
    }
    
    // Check for arrears payment patterns
    const arrearsPatterns = [
        /pelunasan\s+tunggakan/i,
        /pelunasan\s+kas/i,
        /tunggakan/i
    ];
    
    for (const pattern of arrearsPatterns) {
        if (pattern.test(description)) {
            info.isArrearsPayment = true;
            break;
        }
    }
    
    // Extract month and year
    const monthNames = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 
                       'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
    
    for (let i = 0; i < monthNames.length; i++) {
        const monthRegex = new RegExp(monthNames[i], 'i');
        if (monthRegex.test(description)) {
            info.month = monthNames[i].charAt(0).toUpperCase() + monthNames[i].slice(1);
            info.monthNumber = i + 1;
            break;
        }
    }
    
    // Extract year
    const yearMatch = description.match(/\b(20\d{2})\b/);
    if (yearMatch) {
        info.year = yearMatch[1];
    }
    
    return info;
}

// Close transaction detail modal
window.closeTransactionDetailModal = function() {
    const modal = document.getElementById('transactionDetailModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};
</script>

<style>
/* Modal Styles */
.fixed {
    position: fixed;
}

.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.bg-opacity-50 {
    background-color: rgba(0, 0, 0, 0.5);
}

.z-50 {
    z-index: 50;
}

.hidden {
    display: none;
}

.rounded-2xl {
    border-radius: 1rem;
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.max-w-2xl {
    max-width: 42rem;
}

.w-full {
    width: 100%;
}

.max-h-\[90vh\] {
    max-height: 90vh;
}

.overflow-y-auto {
    overflow-y: auto;
}

.border-b {
    border-bottom-width: 1px;
}

.border-gray-200 {
    border-color: #e5e7eb;
}

.p-6 {
    padding: 1.5rem;
}

.text-2xl {
    font-size: 1.5rem;
    line-height: 2rem;
}

.font-bold {
    font-weight: 700;
}

.text-gray-900 {
    color: #111827;
}

.text-gray-400 {
    color: #9ca3af;
}

.hover\:text-gray-600:hover {
    color: #4b5563;
}

.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.w-6 {
    width: 1.5rem;
}

.h-6 {
    height: 1.5rem;
}

.space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1.5rem;
}

.space-y-4 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1rem;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.gap-4 {
    gap: 1rem;
}

.bg-gray-50 {
    background-color: #f9fafb;
}

.rounded-xl {
    border-radius: 0.75rem;
}

.p-4 {
    padding: 1rem;
}

.text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.font-medium {
    font-weight: 500;
}

.text-gray-500 {
    color: #6b7280;
}

.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem;
}

.bg-green {
    background-color: #10b981;
}

.text-green-800 {
    color: #064e3b;
}

.border-green-200 {
    border-color: #86efac;
}

.bg-red {
    background-color: #ef4444;
}

.text-red-800 {
    color: #7f1d1d;
}

.border-red-200 {
    border-color: #fca5a5;
}

.text-green-600 {
    color: #059669;
}

.text-red-600 {
    color: #dc2626;
}

.bg-green-50 {
    background-color: #f0fdf4;
}

.border-green-200 {
    border-color: #86efac;
}

.text-green-800 {
    color: #064e3b;
}

.bg-blue-50 {
    background-color: #eff6ff;
}

.border-blue-200 {
    border-color: #93c5fd;
}

.text-blue-800 {
    color: #1e3a8a;
}

.text-blue-700 {
    color: #1d4ed8;
}

.text-blue-600 {
    color: #2563eb;
}

.text-blue-500 {
    color: #3b82f6;
}

.text-xs {
    font-size: 0.75rem;
    line-height: 1rem;
}

.max-w-xs {
    max-width: 20rem;
}

.shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.cursor-pointer {
    cursor: pointer;
}

.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-2 {
    margin-top: 0.5rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views\admin\monitor-keuangan.blade.php ENDPATH**/ ?>
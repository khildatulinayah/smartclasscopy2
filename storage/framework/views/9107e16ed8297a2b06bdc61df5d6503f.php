<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Monitor Absensi</h1>
                    <p class="greeting-subtitle">Pantau kehadiran siswa per hari (Read-Only)</p>
                </div>
            </section>

            <!-- Date Navigation -->
            <section class="date-navigation">
                <div class="date-nav-card">
                    <div class="date-nav-header">
                        <a href="<?php echo e(route('admin.monitor.absensi')); ?>?date=<?php echo e($prevDate); ?>" class="date-nav-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Sebelumnya
                        </a>
                        <div class="current-date">
                            <h2><?php echo e(\Carbon\Carbon::parse($selectedDate)->locale('id')->format('l, d F Y')); ?></h2>
                            <form method="GET" action="<?php echo e(route('admin.monitor.absensi')); ?>" class="date-picker-form">
                                <input type="date" 
                                       name="date" 
                                       id="datePicker" 
                                       value="<?php echo e($selectedDate); ?>" 
                                       class="date-picker-input"
                                       max="<?php echo e(now()->format('Y-m-d')); ?>">
                                <button type="submit" class="date-picker-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <a href="<?php echo e(route('admin.monitor.absensi')); ?>?date=<?php echo e($nextDate); ?>" class="date-nav-btn">
                            Selanjutnya
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Daily Statistics -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon success">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Hadir</div>
                        </div>
                        <div class="stat-value"><?php echo e($totalHadir); ?></div>
                        <div class="stat-description">Hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon warning">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 9v2m0 4h.01M12 9v2m0 4h.01"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Sakit</div>
                        </div>
                        <div class="stat-value"><?php echo e($totalSakit); ?></div>
                        <div class="stat-description">Hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon info">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Izin</div>
                        </div>
                        <div class="stat-value"><?php echo e($totalIzin); ?></div>
                        <div class="stat-description">Hari ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 0h8m0 0v8m0-2v-2H9a2 2 0 00-2 2H6a2 2 0 00-2 2v2a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v2z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Alpa</div>
                        </div>
                        <div class="stat-value"><?php echo e($totalAlpha); ?></div>
                        <div class="stat-description">Hari ini</div>
                    </div>
                </div>
            </section>

            <!-- Attendance Table -->
            <section class="table-header-section">
                <div class="table-header">
                    <h2 class="table-title">Daftar Absensi - <?php echo e(\Carbon\Carbon::parse($selectedDate)->locale('id')->format('d F Y')); ?></h2>
                    <div class="table-actions">
                        <div class="search-container">
                            <input type="text" id="searchAttendance" placeholder="Cari nama siswa..." class="search-input">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="filter-container">
                            <select id="statusFilter" class="form-input" style="width: 150px;">
                                <option value="">Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpha">Alpa</option>
                                <option value="belum_absen">Belum Absen</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <div class="table-card">
                <div class="table-container">
                    <table class="data-table" id="attendanceTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($isHoliday): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; color: #f59e0b;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div style="font-size: 16px; font-weight: 600;">Hari Libur</div>
                                            <div style="font-size: 14px;"><?php echo e($holiday->note ?? 'Hari libur nasional'); ?></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php elseif($isWeekend): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; color: #6b7280;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <div style="font-size: 16px; font-weight: 600;">Akhir Pekan</div>
                                            <div style="font-size: 14px;"><?php echo e(\Carbon\Carbon::parse($selectedDate)->locale('id')->format('l')); ?> - Tidak ada jadwal absensi</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-name="<?php echo e(strtolower($attendance->student->name)); ?>" data-status="<?php echo e(strtolower($attendance->status)); ?>">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td class="font-semibold"><?php echo e($attendance->student->name); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = 'secondary';
                                            $statusText = 'BELUM ABSEN';
                                            
                                            if ($attendance->status == 'hadir') {
                                                $statusClass = 'success';
                                                $statusText = 'HADIR';
                                            } elseif ($attendance->status == 'sakit') {
                                                $statusClass = 'warning';
                                                $statusText = 'SAKIT';
                                            } elseif ($attendance->status == 'izin') {
                                                $statusClass = 'info';
                                                $statusText = 'IZIN';
                                            } elseif ($attendance->status == 'alpha') {
                                                $statusClass = 'danger';
                                                $statusText = 'ALPA';
                                            }
                                        ?>
                                        <span class="status-badge <?php echo e($statusClass); ?>">
                                            <?php echo e($statusText); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($attendance->keterangan ?? '-'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
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
    grid-template-columns: repeat(4, 1fr); 
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

.stat-icon.info { 
    background: #f3f4f6; 
    color: #6b7280; 
}

.stat-icon.success { 
    background: #dcfce7; 
    color: #10b981; 
}

.stat-icon.warning { 
    background: #fef3c7; 
    color: #f59e0b; 
}

.stat-icon.expense { 
    background: #fee2e2; 
    color: #ef4444; 
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

.form-input { 
    width: 100%; 
    padding: 12px 16px; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    font-size: 14px; 
    transition: all 0.2s; 
    background: white; 
}

.form-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1); 
}

.filter-container { 
    display: flex; 
    align-items: center; 
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

.status-badge.info { 
    background: #dbeafe; 
    color: #3b82f6; 
}

.status-badge.danger { 
    background: #fee2e2; 
    color: #dc2626; 
}

.status-badge.secondary { 
    background: #f3f4f6; 
    color: #374151; 
}

/* Date Navigation Styles */
.date-navigation { 
    margin-bottom: 32px; 
}

.date-nav-card { 
    background: white; 
    border-radius: 16px; 
    padding: 24px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.date-nav-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

.current-date { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 12px; 
}

.current-date h2 { 
    font-size: 24px; 
    font-weight: 700; 
    color: #1e293b; 
    margin: 0; 
}

.date-picker-form { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    background: white; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    padding: 4px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
}

.date-picker-input { 
    border: none; 
    outline: none; 
    padding: 8px 12px; 
    font-size: 14px; 
    color: #1e293b; 
    background: transparent; 
    min-width: 150px; 
}

.date-picker-input::-webkit-calendar-picker-indicator { 
    display: none; 
}

.date-picker-btn { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    background: #3b82f6; 
    color: white; 
    border: none; 
    border-radius: 6px; 
    padding: 8px; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.date-picker-btn:hover { 
    background: #2563eb; 
}

.date-picker-btn svg { 
    width: 16px; 
    height: 16px; 
}

.date-nav-btn { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    background: #f8fafc; 
    color: #64748b; 
    border: 1px solid #e2e8f0; 
    padding: 12px 20px; 
    border-radius: 8px; 
    text-decoration: none; 
    font-weight: 600; 
    transition: all 0.2s ease; 
}

.date-nav-btn:hover { 
    background: #3b82f6; 
    color: white; 
    border-color: #3b82f6; 
}

.date-nav-btn svg { 
    width: 20px; 
    height: 20px; 
}

/* Responsive */
@media (max-width: 1200px) { 
    .stats-grid { 
        grid-template-columns: repeat(2, 1fr); 
    } 
}

@media (max-width: 768px) { 
    .stats-grid { 
        grid-template-columns: 1fr; 
        gap: 16px; 
    } 
    .sidebar { 
        width: 260px; 
    } 
    .main-content { 
        padding: 20px; 
    } 
    .date-nav-header { 
        flex-direction: column; 
        gap: 12px; 
    } 
    .date-picker-form { 
        flex-direction: column; 
        gap: 8px; 
        width: 100%; 
    } 
    .date-picker-input { 
        width: 100%; 
        min-width: auto; 
    } 
    .date-picker-btn { 
        width: 100%; 
        justify-content: center; 
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
    // Search functionality for attendance table
    const searchInput = document.getElementById('searchAttendance');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('#attendanceTable tbody tr');
    
    function filterTable() {
        const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
        const statusQuery = statusFilter ? statusFilter.value : '';
        
        rows.forEach(row => {
            const name = row.dataset.name;
            const status = row.dataset.status;
            
            const nameMatch = !searchQuery || name.includes(searchQuery);
            const statusMatch = !statusQuery || status.includes(statusQuery);
            
            if (nameMatch && statusMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
    
    // Date picker functionality
    const datePicker = document.getElementById('datePicker');
    const datePickerForm = document.querySelector('.date-picker-form');
    
    if (datePicker && datePickerForm) {
        // Show native date picker on click
        datePicker.addEventListener('click', function() {
            this.showPicker();
        });
        
        // Handle form submission
        datePickerForm.addEventListener('submit', function(e) {
            // Form will submit normally, no need for extra handling
        });
        
        // Handle date change for immediate navigation
        datePicker.addEventListener('change', function() {
            // Auto-submit when date changes
            this.form.submit();
        });
        
        // Add keyboard navigation
        datePicker.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
    
    // Add today button functionality
    const todayBtn = document.createElement('button');
    todayBtn.type = 'button';
    todayBtn.innerHTML = 'Hari Ini';
    todayBtn.className = 'today-btn';
    todayBtn.style.cssText = `
        background: #10b981;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        cursor: pointer;
        margin-left: 8px;
        transition: all 0.2s ease;
    `;
    
    todayBtn.addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        window.location.href = `<?php echo e(route('admin.monitor.absensi')); ?>?date=${today}`;
    });
    
    todayBtn.addEventListener('mouseenter', function() {
        this.style.background = '#059669';
    });
    
    todayBtn.addEventListener('mouseleave', function() {
        this.style.background = '#10b981';
    });
    
    // Add today button next to date picker form
    if (datePickerForm) {
        datePickerForm.parentNode.appendChild(todayBtn);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/admin/monitor_absensi.blade.php ENDPATH**/ ?>
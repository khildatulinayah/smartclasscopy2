<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <?php echo $__env->make('components.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Kelola Data Siswa</h1>
                    <p class="greeting-subtitle">Tambah, edit, dan hapus data siswa dengan mudah</p>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon info">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0zm1 3a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Total Siswa</div>
                        </div>
                        <div class="stat-value"><?php echo e($students->count()); ?></div>
                        <div class="stat-description">Siswa terdaftar</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon income">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Aktif</div>
                        </div>
                        <div class="stat-value"><?php echo e($students->where('is_active', 1)->count()); ?></div>
                        <div class="stat-description">Siswa aktif saat ini</div>
                    </div>
                </div>
            </section>

            <!-- Table Header with Search & Add -->
            <section class="table-header-section">
                <div class="table-header">
                    <h2 class="table-title">Daftar Siswa</h2>
                    <div class="table-actions">
                        <div class="search-container">
                            <input type="text" id="searchInput" placeholder="Cari nama atau email..." class="search-input">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <a href="<?php echo e(route('admin.students.create')); ?>" class="feature-btn">+ Tambah Siswa</a>
                    </div>
                </div>
            </section>

            <!-- Students Table -->
            <div class="table-card">
                <div class="table-container">
                    <table class="data-table" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-no="<?php echo e($index + 1); ?>" data-name="<?php echo e(strtolower($student->name)); ?>" data-email="<?php echo e(strtolower($student->email)); ?>" data-role="<?php echo e(strtolower($student->role)); ?>">
                                <td class="text-center font-semibold"><?php echo e($index + 1); ?></td>
                                <td>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($student->name)); ?>&background=3b82f6&color=fff&size=40" alt="<?php echo e($student->name); ?>" class="avatar-img">
                                </td>
                                <td class="font-semibold"><?php echo e($student->name); ?></td>
                                <td><?php echo e($student->email); ?></td>
                                <td>
                                    <span class="status-badge role">
                                        <?php echo e(ucfirst($student->role)); ?>

                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo e($student->is_active ? 'success' : 'warning'); ?>">
                                        <?php echo e($student->is_active ? '✓ Aktif' : '⚠ Tidak Aktif'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($student->created_at->format('d M Y')); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo e(route('admin.students.edit', $student->id)); ?>" class="btn-edit">✏️ Edit</a>
                                        <form method="POST" action="<?php echo e(route('admin.students.delete', $student->id)); ?>" class="delete-form" onsubmit="return confirm('Yakin hapus <?php echo e($student->name); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn-delete">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if(isset($students) && method_exists($students, 'links')): ?>
            <div class="pagination-container">
                <?php echo e($students->links()); ?>

            </div>
            <?php endif; ?>
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
    grid-template-columns: repeat(2, 1fr); 
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

.stat-icon.income { 
    background: #dcfce7; 
    color: #10b981; 
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

.feature-btn { 
    display: inline-block; 
    background: #3b82f6; 
    color: white; 
    border: none; 
    padding: 12px 24px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    text-decoration: none; 
    transition: all 0.2s ease; 
}

.feature-btn:hover { 
    background: #2563eb; 
    transform: translateY(-1px); 
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

.avatar-img { 
    width: 40px; 
    height: 40px; 
    border-radius: 8px; 
    object-fit: cover; 
    border: 2px solid #e2e8f0; 
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

.status-badge.role { 
    background: #dbeafe; 
    color: #3b82f6; 
}

.status-badge.success { 
    background: #dcfce7; 
    color: #166534; 
}

.status-badge.warning { 
    background: #fef3c7; 
    color: #92400e; 
}

/* Action Buttons */
.action-buttons { 
    display: flex; 
    gap: 8px; 
}

.btn-edit { 
    display: inline-block; 
    background: #3b82f6; 
    color: white; 
    border: none; 
    padding: 8px 12px; 
    border-radius: 6px; 
    font-size: 12px; 
    font-weight: 600; 
    cursor: pointer; 
    text-decoration: none; 
    transition: all 0.2s ease; 
}

.btn-edit:hover { 
    background: #2563eb; 
    transform: translateY(-1px); 
}

.btn-delete { 
    background: #ef4444; 
    color: white; 
    border: none; 
    padding: 8px 12px; 
    border-radius: 6px; 
    font-size: 12px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.btn-delete:hover { 
    background: #dc2626; 
    transform: translateY(-1px); 
}

.delete-form { 
    margin: 0; 
    display: inline; 
}

/* Pagination */
.pagination-container { 
    text-align: center; 
    margin-top: 32px; 
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
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        rows.forEach(row => {
            const name = row.dataset.name;
            const email = row.dataset.email;
            const role = row.dataset.role;
            if (name.includes(query) || email.includes(query) || role.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/admin/students.blade.php ENDPATH**/ ?>
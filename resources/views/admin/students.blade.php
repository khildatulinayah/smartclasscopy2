@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.admin-sidebar')

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
                        <div class="stat-value">{{ $students->count() }}</div>
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
                        <div class="stat-value">{{ $students->where('is_active', 1)->count() }}</div>
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
                        <a href="{{ route('admin.students.create') }}" class="feature-btn">+ Tambah Siswa</a>
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
                            @foreach($students as $index => $student)
                            <tr data-no="{{ $index + 1 }}" data-name="{{ strtolower($student->name) }}" data-email="{{ strtolower($student->email) }}" data-role="{{ strtolower($student->role) }}">
                                <td class="text-center font-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ $student->profile_photo ? asset('storage/' . $student->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=3b82f6&color=fff&size=40' }}" alt="{{ $student->name }}" class="avatar-img">
                                </td>
                                <td class="font-semibold">{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    <span class="status-badge role">
                                        {{ ucfirst($student->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $student->is_active ? 'success' : 'warning' }}">
                                        {{ $student->is_active ? '✓ Aktif' : '⚠ Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>{{ $student->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn-edit">✏️ Edit</a>
                                        <button type="button" class="btn-delete" onclick="showDeleteModal('{{ $student->id }}', '{{ $student->name }}')">🗑️ Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if(isset($students) && method_exists($students, 'links'))
            <div class="pagination-container">
                {{ $students->links() }}
            </div>
            @endif
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 transform transition-all">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700">Apakah Anda yakin ingin menghapus siswa <span id="deleteStudentName" class="font-semibold"></span>? Semua data terkait akan dihapus permanen.</p>
        </div>
        
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal-backdrop {
    backdrop-filter: blur(4px);
}

.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-overlay {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Ensure modal is visible when shown */
#deleteModal:not(.hidden) {
    display: flex !important;
}

/* Button hover effects */
.bg-red-600:hover {
    background-color: #dc2626 !important;
}

.bg-gray-200:hover {
    background-color: #e5e7eb !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .max-w-md {
        max-width: 90vw !important;
    }
}
</style>

<script>
function showDeleteModal(studentId, studentName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('deleteStudentName');
    
    // Set form action
    form.action = '/admin/students/' + studentId;
    
    // Set student name
    nameSpan.textContent = studentName;
    
    // Show modal with animation
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'modal-overlay');
    
    // Add animation to modal content
    const modalContent = modal.querySelector('.bg-white');
    modalContent.classList.add('modal-content');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    
    // Hide modal
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'modal-overlay');
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
    
    // Clear form
    const form = document.getElementById('deleteForm');
    form.reset();
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideDeleteModal();
    }
});
</script>
@endsection

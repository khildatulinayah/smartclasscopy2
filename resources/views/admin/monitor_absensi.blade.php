@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
                <span class="logo-text">SMARTCLASS</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.students') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0zm1 3a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>CRUD Siswa</span>
            </a>
            <a href="{{ route('admin.monitor.absensi') }}" class="nav-item active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Absensi</span>
            </a>
            <a href="{{ route('admin.monitor.kas') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Kas</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Laporan</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-profile-mini">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff" alt="User" class="user-avatar-mini">
                <div class="user-info-mini">
                    <div class="user-name-mini">{{ auth()->user()->name }}</div>
                    <div class="user-role-mini">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
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
                    <h1 class="greeting-title">Monitor Absensi</h1>
                    <p class="greeting-subtitle">Pantau kehadiran siswa per hari (Read-Only)</p>
                </div>
            </section>

            <!-- Date Navigation -->
            <section class="date-navigation">
                <div class="date-nav-card">
                    <div class="date-nav-header">
                        <a href="{{ route('admin.monitor.absensi') }}?date={{ $prevDate }}" class="date-nav-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Sebelumnya
                        </a>
                        <div class="current-date">
                            <h2>{{ \Carbon\Carbon::parse($selectedDate)->locale('id')->format('l, d F Y') }}</h2>
                        </div>
                        <a href="{{ route('admin.monitor.absensi') }}?date={{ $nextDate }}" class="date-nav-btn">
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
                        <div class="stat-value">{{ $totalHadir }}</div>
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
                        <div class="stat-value">{{ $totalSakit }}</div>
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
                        <div class="stat-value">{{ $totalIzin }}</div>
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
                        <div class="stat-value">{{ $totalAlpha }}</div>
                        <div class="stat-description">Hari ini</div>
                    </div>
                </div>
            </section>

            <!-- Attendance Table -->
            <section class="table-header-section">
                <div class="table-header">
                    <h2 class="table-title">Daftar Absensi - {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->format('d F Y') }}</h2>
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
                            @if($attendances->count() > 0)
                                @foreach($attendances as $index => $attendance)
                                <tr data-name="{{ strtolower($attendance->student->name) }}" data-status="{{ strtolower($attendance->status) }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-semibold">{{ $attendance->student->name }}</td>
                                    <td>
                                        <span class="status-badge {{ $attendance->status == 'hadir' ? 'success' : ($attendance->status == 'sakit' ? 'warning' : ($attendance->status == 'izin' ? 'info' : 'danger')) }}">
                                            {{ strtoupper($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #64748b;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; color: #cbd5e1;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div style="font-size: 16px; font-weight: 600;">Tidak ada data absensi</div>
                                            <div style="font-size: 14px;">Belum ada siswa yang melakukan absensi pada tanggal ini</div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
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

.current-date h2 { 
    font-size: 24px; 
    font-weight: 700; 
    color: #1e293b; 
    margin: 0; 
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
    .date-nav-header { 
        flex-direction: column; 
        gap: 12px; 
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
});
</script>
@endsection

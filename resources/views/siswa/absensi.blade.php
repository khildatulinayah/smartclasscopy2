@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.siswa-sidebar')

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
                        <div class="user-name">{{ auth()->user()->name }}</div>
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
                    <h1>Riwayat Absensi</h1>
                    <p>Lihat rekam kehadiran Anda selama periode ini</p>
                </div>
                <div class="page-actions">
                    <div class="month-navigation">
                        <a href="{{ route('siswa.absensi.month', [$prevMonth->month, $prevMonth->year]) }}" class="nav-btn prev-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Prev</span>
                        </a>
                        
                        <div class="current-month">
                            <span class="month-label">{{ $currentDate->translatedFormat('F Y') }}</span>
                        </div>
                        
                        <a href="{{ route('siswa.absensi.month', [$nextMonth->month, $nextMonth->year]) }}" class="nav-btn next-btn">
                            <span>Next</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Statistics Overview -->
            <section class="stats-overview">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon present">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $totalHadir ?? 0 }}</div>
                            <div class="stat-label">Hadir</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon sick">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $totalSakit ?? 0 }}</div>
                            <div class="stat-label">Sakit</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon permit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $totalIzin ?? 0 }}</div>
                            <div class="stat-label">Izin</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon absent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $totalAlpha ?? 0 }}</div>
                            <div class="stat-label">Alpa</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Attendance Table -->
            <section class="attendance-table-section">
                <div class="section-header">
                    <h2>Detail Kehadiran</h2>
                    <div class="table-actions">
                        <button class="export-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances ?? [] as $attendance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'hadir' => ['label' => 'Hadir', 'class' => 'hadir'],
                                                'sakit' => ['label' => 'Sakit', 'class' => 'sakit'],
                                                'izin' => ['label' => 'Izin', 'class' => 'izin'],
                                                'alpha' => ['label' => 'Alpa', 'class' => 'alpha'],
                                                'belum_absen' => ['label' => 'Belum Absen', 'class' => 'belum_absen']
                                            ];
                                            
                                            // Jika status libur, tambahkan konfigurasi libur
                                            if ($attendance->status === 'libur') {
                                                $statusConfig['libur'] = ['label' => '📅 ' . ($attendance->holiday_note ?? 'Hari Libur'), 'class' => 'libur'];
                                            }
                                            
                                            $status = $statusConfig[$attendance->status] ?? $statusConfig['belum_absen'];
                                        @endphp
                                        <span class="status-badge {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->description ?? ($attendance->holiday_note ?? '-') }}</td>
                                    <td>{{ $attendance->check_in ?? '-' }}</td>
                                    <td>{{ $attendance->check_out ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="no-data">Belum ada data absensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
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

.month-selector {
    padding: 10px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    background: white;
    cursor: pointer;
}

/* Month Navigation */
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

/* Stats Overview */
.stats-overview {
    margin-bottom: 32px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon.present {
    background: #dcfce7;
    color: #10b981;
}

.stat-icon.sick {
    background: #fef3c7;
    color: #f59e0b;
}

.stat-icon.permit {
    background: #dbeafe;
    color: #3b82f6;
}

.stat-icon.absent {
    background: #fee2e2;
    color: #ef4444;
}

.stat-icon svg {
    width: 24px;
    height: 24px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: #64748b;
    margin-top: 4px;
}

/* Table Section */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
}

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

/* Table Styles */
.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
}

.attendance-table th {
    background: #f8fafc;
    padding: 16px;
    text-align: left;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.attendance-table td {
    padding: 16px;
    font-size: 14px;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
}

.attendance-table tr:last-child td {
    border-bottom: none;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.hadir {
    background: #dcfce7;
    color: #10b981;
}

.status-badge.sakit {
    background: #fef3c7;
    color: #f59e0b;
}

.status-badge.izin {
    background: #dbeafe;
    color: #3b82f6;
}

.status-badge.alpa {
    background: #fee2e2;
    color: #ef4444;
}

.status-badge.belum_absen {
    background: #f3f4f6;
    color: #6b7280;
}

.status-badge.libur {
    background: #e0e7ff;
    color: #3730a3;
}

.no-data {
    text-align: center;
    color: #64748b;
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .attendance-table {
        min-width: 600px;
    }
}
</style>
@endsection

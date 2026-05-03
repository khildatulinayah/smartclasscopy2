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
            <!-- Greeting Section -->
<section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Selamat {{ \Carbon\Carbon::now()->hour < 12 ? 'Pagi' : (\Carbon\Carbon::now()->hour < 15 ? 'Siang' : (\Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam')) }}, {{ auth()->user()->name }}! 👋</h1>
                    <p class="greeting-subtitle">Mari kita lihat progress belajar dan pembayaranmu hari ini</p>
                </div>
            </section>

        <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stats-grid">
                    <!-- Profile Card -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon profile">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Profil Saya</div>
                        </div>
                        <div class="profile-info">
                            <div class="info-row">
                                <span class="info-label">Nama:</span>
<span class="info-value">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">NIS:</span>
<span class="info-value">{{ auth()->user()->student->nis ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kelas:</span>
<span class="info-value">{{ auth()->user()->student->class ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Card -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon attendance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Kehadiran Saya</div>
                        </div>
                        <div class="attendance-circle">
                            <div class="circle-progress">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring__background" stroke="#e2e8f0" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
stroke-dashoffset="{{ $totalDays > 0 ? 326.73 - ($totalHadir / $totalDays * 326.73) : 326.73 }}"/>
                                </svg>
<div class="progress-text">{{ $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 0) : 0 }}%</div>
                            </div>
                        </div>
                        <div class="attendance-breakdown">
                            <div class="breakdown-item">
                                <div class="status-dot present"></div>
<span>Hadir: {{ $totalHadir }}</span>
                            </div>
                            <div class="breakdown-item">
                                <div class="status-dot sick"></div>
<span>Sakit: {{ $totalSakit }}</span>
                            </div>
                            <div class="breakdown-item">
                                <div class="status-dot permit"></div>
<span>Izin: {{ $totalIzin }}</span>
                            </div>
                            <div class="breakdown-item">
                                <div class="status-dot absent"></div>
<span>Alpa: {{ $totalAlpha }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Card -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon payment">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Status Pembayaran</div>
                        </div>
<div class="payment-month">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
                        <div class="payment-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 75%"></div>
                            </div>
                            <div class="progress-details">
<span class="progress-label">Lunas {{ $paidWeeks }}/{{ $totalWeeks }} Minggu</span>
<span class="progress-amount">Rp {{ number_format($kasSudahBayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="payment-weeks">
@foreach($weeklyPayments as $payment)
                                @php
                                    $paidClass = $payment->status == 'paid' ? 'paid' : 'unpaid';
                                    $statusIcon = $payment->status == 'paid' ? '✓' : '○';
                                @endphp
                                <div class="week-item {{ $paidClass }}">
                                    <span class="week-label">Minggu {{ $payment->week_number }}</span>
                                    <span class="week-status">{{ $statusIcon }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <!-- Announcements Section -->
            <section class="announcements-section">
                <div class="section-header">
                    <h2 class="section-title">Pengumuman Terbaru</h2>
                    <button class="view-all-btn">
                        Lihat Semua
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                <div class="announcements-grid">
                    <div class="announcement-card">
                        <div class="announcement-icon info">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="announcement-content">
                            <h3 class="announcement-title">Libur Hari Raya</h3>
                            <p class="announcement-description">Sekolah akan libur dari tanggal 10-15 April 2025 untuk merayakan Hari Raya Idul Fitri.</p>
                            <div class="announcement-meta">
                                <span class="announcement-date">2 hari yang lalu</span>
                                <span class="announcement-category">Pengumuman</span>
                            </div>
                        </div>
                    </div>

                    <div class="announcement-card">
                        <div class="announcement-icon success">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="announcement-content">
                            <h3 class="announcement-title">UTS Semester Genap</h3>
                            <p class="announcement-description">Ujian Tengah Semester akan dimulai pada tanggal 20 April 2025. Persiapkan diri Anda dengan baik.</p>
                            <div class="announcement-meta">
                                <span class="announcement-date">5 hari yang lalu</span>
                                <span class="announcement-category">Akademik</span>
                            </div>
                        </div>
                    </div>

                    <div class="announcement-card">
                        <div class="announcement-icon warning">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a2 2 0 01-2-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2v-4m0 0V6a2 2 0 012-2h2a2 2 0 012-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2a2 2 0 002-2v-4"></path>
                            </svg>
                        </div>
                        <div class="announcement-content">
                            <h3 class="announcement-title">Jam Masuk Ditunda</h3>
                            <p class="announcement-description">Besok jam masuk sekolah ditunda menjadi pukul 08:00 karena rapat guru.</p>
                            <div class="announcement-meta">
                                <span class="announcement-date">1 hari yang lalu</span>
                                <span class="announcement-category">Informasi</span>
                            </div>
                        </div>
                    </div>
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

/* ===== GREETING SECTION ===== */
.greeting-section {
    margin-bottom: 32px;
}

.greeting-title {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.greeting-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
    margin-bottom: 32px; 
}
.greeting-subtitle {
    font-size: 16px;
    color: #64748b;
}

/* ===== STATS SECTION ===== */
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
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

.stat-icon.profile {
    background: #dbeafe;
    color: #3b82f6;
}

.stat-icon.attendance {
    background: #dcfce7;
    color: #10b981;
}

.stat-icon.payment {
    background: #fef3c7;
    color: #f59e0b;
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

/* ===== PROFILE INFO ===== */
.profile-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}

.info-label {
    font-size: 14px;
    color: #64748b;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

/* ===== ATTENDANCE CIRCLE ===== */
.attendance-circle {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.circle-progress {
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

/* ===== ATTENDANCE BREAKDOWN ===== */
.attendance-breakdown {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.breakdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 6px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot.present {
    background: #10b981;
}

.status-dot.sick {
    background: #f59e0b;
}

.status-dot.permit {
    background: #3b82f6;
}

.status-dot.absent {
    background: #ef4444;
}

/* ===== PAYMENT SECTION ===== */
.payment-month {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 16px;
    text-align: center;
}

.payment-progress {
    margin-bottom: 20px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-details {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
}

.progress-text {
    font-size: 14px;
    color: #64748b;
}

.progress-amount {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

/* ===== PAYMENT WEEKS ===== */
.payment-weeks {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.week-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}

.week-item.paid {
    background: #dcfce7;
}

.week-item.unpaid {
    background: #fee2e2;
}

.week-label {
    font-size: 14px;
    color: #64748b;
}

.week-status {
    font-size: 16px;
    font-weight: 600;
}

.week-item.paid .week-status {
    color: #10b981;
}

.week-item.unpaid .week-status {
    color: #ef4444;
}

/* ===== ANNOUNCEMENTS SECTION ===== */
.announcements-section {
    margin-bottom: 32px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.view-all-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: none;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.view-all-btn:hover {
    background: #f8fafc;
    color: #3b82f6;
    border-color: #3b82f6;
}

.view-all-btn svg {
    width: 16px;
    height: 16px;
}

.announcements-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.announcement-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.announcement-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.announcement-icon.info {
    background: #dbeafe;
    color: #3b82f6;
}

.announcement-icon.success {
    background: #dcfce7;
    color: #10b981;
}

.announcement-icon.warning {
    background: #fef3c7;
    color: #f59e0b;
}

.announcement-icon svg {
    width: 20px;
    height: 20px;
}

.announcement-title {
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
}

.announcement-description {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 16px;
    line-height: 1.5;
}

.announcement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.announcement-date {
    font-size: 12px;
    color: #64748b;
}

.announcement-category {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #64748b;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .announcements-grid {
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
    
    .greeting-title {
        font-size: 24px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .topbar {
        padding: 0 20px;
    }
    
    .user-info {
        display: none;
    }
    
    .payment-weeks {
        grid-template-columns: 1fr;
    }
}

/* ===== SCROLLBAR STYLING ===== */
.main-content::-webkit-scrollbar {
    width: 6px;
}

.main-content::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.main-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.main-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
// Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // View all button
    const viewAllBtn = document.querySelector('.view-all-btn');
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            console.log('View all announcements');
            // Add navigation logic here
        });
    }

    // Notification button
    const notificationBtn = document.querySelector('.notification-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            console.log('Opening notifications...');
            // Add notification dropdown logic here
        });
    }

    // User profile dropdown
    const userProfile = document.querySelector('.user-profile');
    if (userProfile) {
        userProfile.addEventListener('click', function() {
            console.log('Opening user profile menu...');
            // Add profile dropdown logic here
        });
    }

    // Menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            console.log('Toggling sidebar...');
            // Add sidebar toggle logic here
        });
    }

    // Animate progress circles on load
    const progressRings = document.querySelectorAll('.progress-ring__progress');
    progressRings.forEach(ring => {
        const offset = ring.style.strokeDashoffset;
        ring.style.strokeDashoffset = '326.73';
        setTimeout(() => {
            ring.style.strokeDashoffset = offset;
        }, 100);
    });
});
</script>
@endsection

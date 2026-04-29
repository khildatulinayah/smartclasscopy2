@extends('layouts.app')

@section('title', 'Pembayaran Mingguan - Sederhana')

@section('content')
<div class="dashboard-layout">
    <!-- Same sidebar as dashboard/weekly -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
                <span class="logo-text">SMARTCLASS</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('bendahara.dashboard') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('bendahara.kas') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Keuangan</span>
            </a>
            <a href="{{ route('bendahara.weekly.payments') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Pembayaran Mingguan</span>
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
            <!-- Greeting -->
            <section class="greeting-section mb-8">
                <div class="greeting-card">
                    <h1 class="greeting-title">Pembayaran Mingguan Siswa</h1>
                    <p class="greeting-subtitle">Tabel sederhana untuk tracking status pembayaran 4 minggu per bulan</p>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="stats-section mb-8">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon payment">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                </div>
            </section>

            <!-- Payment Table -->
            <section class="tables-section">
                <div class="table-card">
                    <div class="table-header">
                        <h2 class="table-title">Status Pembayaran Mingguan</h2>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th class="text-center">Minggu 1</th>
                                    <th class="text-center">Minggu 2</th>
                                    <th class="text-center">Minggu 3</th>
                                    <th class="text-center">Minggu 4</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentsByStudent ?? [] as $studentId => $payments)
                                @php
                                    $paidCount = $payments->where('status', 'paid')->count();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="font-semibold">{{ $payments->first()->student->name ?? 'Unknown' }}</td>
                                    @for($week = 1; $week <= 4; $week++)
                                        @php
                                            $payment = $payments->where('week_number', $week)->first();
                                            $status = $payment && $payment->status == 'paid' ? 'success' : 'danger';
                                            $icon = $status == 'success' ? '✓' : '✗';
                                        @endphp
                                        <td class="text-center">
                                            <span class="status-badge {{ $status }}">{{ $icon }}</span>
                                        </td>
                                    @endfor
                                    <td class="text-center font-bold">
                                        <span class="status-badge {{ $paidCount == 4 ? 'success' : 'danger' }}">
                                            {{ $paidCount }}/4
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        Belum ada data pembayaran
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Dashboard CSS -->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
/* All dashboard CSS copied from dashboard.blade.php - sidebar, main-area, cards, tables, responsive */
.sidebar-header { padding: 24px 20px; border-bottom: 1px solid #e2e8f0; }
.logo { display: flex; align-items: center; gap: 12px; }
.logo-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
.logo-text { font-size: 20px; font-weight: 700; color: #1e293b; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #64748b; text-decoration: none; transition: all 0.2s ease; border-radius: 0 8px 8px 0; margin: 0 12px; }
.nav-item:hover { background: #f8fafc; color: #3b82f6; }
.nav-item.active { background: #eff6ff; color: #3b82f6; font-weight: 600; }
.nav-icon { width: 20px; height: 20px; }
.sidebar-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; }
.user-profile-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.user-avatar-mini { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }
.user-name-mini { font-size: 13px; font-weight: 600; color: #1e293b; }
.user-role-mini { font-size: 11px; color: #64748b; }
.logout-form { display: block; }
.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.logout-btn:hover { background: #fecaca; }
.logout-icon { width: 16px; height: 16px; }
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.main-content { flex: 1; padding: 32px; overflow-y: auto; }
.greeting-section { margin-bottom: 32px; }
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.stats-section, .feature-cards { margin-bottom: 32px; }
.stats-grid, .feature-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
.stat-card, .feature-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.feature-icon, .stat-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; }
.feature-icon svg, .stat-icon svg { width: 32px; height: 32px; }
.feature-icon.green { background: #dcfce7; color: #10b981; }
.feature-icon.orange { background: #fed7aa; color: #f97316; }
.feature-icon.blue { background: #dbeafe; color: #3b82f6; }
.stat-icon.balance { background: #dbeafe; color: #3b82f6; }
.stat-icon.income { background: #dcfce7; color: #10b981; }
.stat-icon.expense { background: #fee2e2; color: #ef4444; }
.stat-icon.payment { background: #e0e7ff; color: #6366f1; }
.stat-icon.remaining { background: #fef3c7; color: #f59e0b; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.stat-description { font-size: 14px; color: #64748b; }
.tables-section { margin-bottom: 32px; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }
.table-title { font-size: 20px; font-weight: 600; color: #1e293b; }
.table-container { padding: 24px; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
.data-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.data-table tr:hover td { background: #f8fafc; }
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.success { background: #dcfce7; color: #166534; }
.status-badge.danger { background: #fee2e2; color: #dc2626; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
</style>
@endsection


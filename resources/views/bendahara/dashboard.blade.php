@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    @include('components.bendahara-sidebar')

    <div class="main-area">
        <main class="main-content">
<section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Selamat {{ \Carbon\Carbon::now()->hour < 12 ? 'Pagi' : (\Carbon\Carbon::now()->hour < 15 ? 'Siang' : (\Carbon\Carbon::now()->hour < 18 ? 'Sore' : 'Malam')) }}, {{ auth()->user()->name }}!</h1>
                    <p class="greeting-subtitle">Mari kita mulai hari yang produktif dengan mengelola keuangan kelas</p>
                </div>
            </section>

            <section class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <h3 class="feature-title">Keuangan</h3>
                    <p class="feature-description">Kelola pemasukan dan pengeluaran kas kelas</p>
                    <a href="{{ route('bendahara.kas') }}" class="feature-btn">Kelola Keuangan</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                    <h3 class="feature-title">Pembayaran Mingguan</h3>
                    <p class="feature-description">Tracking pembayaran kas mingguan siswa</p>
                    <a href="{{ route('bendahara.simple.weekly.payments') }}" class="feature-btn">Lihat Pembayaran</a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon purple"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 10v2a2 2 0 01-2 2H9m-3-8h12M17 8v4m0 0H9"></path></svg></div>
                    <h3 class="feature-title">Cetak Laporan</h3>
                    <p class="feature-description">Laporan kas dan pembayaran siswa per bulan</p>
                    <a href="{{ route('bendahara.laporan') }}" class="feature-btn">Cetak Laporan</a>
                </div>
                            </section>

            @php $paymentPercent = $totalBills > 0 ? round(($paidBills / $totalBills) * 100) : 0; @endphp

            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon balance"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div class="stat-title">Total Saldo Kas</div></div>
                        <div class="stat-value">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                        <div class="stat-description">Total saldo kas kelas saat ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon income"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div class="stat-title">Uang Masuk</div></div>
                        <div class="stat-value">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
                        <div class="stat-description">Total pemasukan bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon expense"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 0l-8 8-4-4-6 6"></path></svg></div><div class="stat-title">Uang Keluar</div></div>
                        <div class="stat-value">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
                        <div class="stat-description">Total pengeluaran bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon remaining"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3v4m0 4h2a2 2 0 002 2v-4a2 2 0 00-2-2h-2a2 2 0 00-2-2v-4m2 6v-4h2a2 2 0 002 2v4a2 2 0 002 2z"></path></svg></div><div class="stat-title">Sisa Kas</div></div>
                        <div class="stat-value">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                        <div class="stat-description">Saldo kas tersisa</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon payment"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path></svg></div><div class="stat-title">Status Pembayaran</div></div>
                        <div class="progress-container">
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ $paymentPercent }}%"></div></div>
                            <div class="progress-text">{{ $paidBills }}/{{ $totalBills }} Lunas</div>
                        </div>
                        <div class="stat-details">
                            <div class="detail-item"><span class="detail-label">Sudah Bayar</span><span class="detail-value">{{ $paidBills }} siswa</span></div>
                            <div class="detail-item"><span class="detail-label">Belum Bayar</span><span class="detail-value">{{ $unpaidBills }} siswa</span></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><div class="stat-icon info"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div><div class="stat-title">Informasi Kelas</div></div>
                        <div class="class-info">
                            <div class="info-row"><span class="info-label">Total Siswa:</span><span class="info-value">{{ $totalStudents }} siswa</span></div>
                            <div class="info-row"><span class="info-label">Semester:</span><span class="info-value">Genap 2026</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="tables-section">
                <div class="table-card">
                    <div class="table-header"><h2 class="table-title">Riwayat Pembayaran</h2></div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead><tr><th>Tanggal</th><th>Nama Siswa</th><th>Minggu</th><th>Jumlah</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentPayments as $rp)
                                <tr>
                                    <td>{{ $rp->payment_date ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y') : '-' }}</td>
                                    <td>{{ $rp->student->name ?? '-' }}</td>
                                    <td>Minggu {{ $rp->week_number }}</td>
                                    <td>Rp {{ number_format($rp->amount, 0, ',', '.') }}</td>
                                    <td><span class="status-badge success">Lunas</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-gray-500">Belum ada riwayat pembayaran</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
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
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
    margin-bottom: 32px; 
}
.greeting-subtitle { font-size: 16px; color: #64748b; }
.feature-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }
.feature-card { background: white; padding: 32px 24px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; text-align: center; transition: all 0.2s ease; }
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.feature-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.feature-icon.blue { background: #dbeafe; color: #3b82f6; }
.feature-icon.green { background: #dcfce7; color: #10b981; }
.feature-icon.orange { background: #fed7aa; color: #f97316; }
.feature-icon svg { width: 32px; height: 32px; }
.feature-title { font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
.feature-description { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
.feature-btn { display: inline-block; background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
.feature-btn:hover { background: #2563eb; }
.stats-section { margin-bottom: 32px; }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.stat-icon.balance { background: #dbeafe; color: #3b82f6; }
.stat-icon.income { background: #dcfce7; color: #10b981; }
.stat-icon.expense { background: #fee2e2; color: #ef4444; }
.stat-icon.remaining { background: #fef3c7; color: #f59e0b; }
.stat-icon.payment { background: #e0e7ff; color: #6366f1; }
.stat-icon.info { background: #f3f4f6; color: #6b7280; }
.stat-icon svg { width: 20px; height: 20px; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; }
.stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.stat-description { font-size: 14px; color: #64748b; }
.progress-container { margin-bottom: 16px; }
.progress-bar { width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); border-radius: 4px; transition: width 0.3s ease; }
.progress-text { text-align: center; font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 8px; }
.stat-details { display: flex; justify-content: space-between; gap: 16px; }
.detail-item { flex: 1; text-align: center; padding: 12px; background: #f8fafc; border-radius: 8px; }
.detail-label { font-size: 12px; color: #64748b; display: block; margin-bottom: 4px; }
.detail-value { font-size: 14px; font-weight: 600; color: #1e293b; }
.class-info { display: flex; flex-direction: column; gap: 12px; }
.info-row { display: flex; justify-content: space-between; padding: 12px; background: #f8fafc; border-radius: 8px; }
.info-label { font-size: 14px; color: #64748b; }
.info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
.tables-section { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.table-title { font-size: 18px; font-weight: 600; color: #1e293b; }
.table-container { padding: 24px; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.data-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 14px; }
.data-table tr:hover td { background: #f8fafc; }
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.success { background: #dcfce7; color: #166534; }
.attendance-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.attendance-badge.hadir { background: #dcfce7; color: #166534; }
.attendance-badge.sakit { background: #fef3c7; color: #92400e; }
.attendance-badge.izin { background: #dbeafe; color: #1e40af; }
.attendance-badge.alpha { background: #fee2e2; color: #991b1b; }
.attendance-badge.belum_absen { background: #f3f4f6; color: #374151; }
.text-center { text-align: center; }
.py-4 { padding-top: 16px; padding-bottom: 16px; }
.text-gray-500 { color: #6b7280; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .feature-cards { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: 1fr; gap: 16px; } }
</style>
@endsection


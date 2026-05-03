@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.sekretaris-sidebar')

    <div class="main-area">
        <main class="main-content">
            <!-- Header with Month Selector -->
            <section class="greeting-section mb-8">
                <div class="greeting-card">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6">
                        <div>
                            <h1 class="greeting-title">Rekap Absensi Bulanan</h1>
                            <p class="greeting-subtitle">{{ \Carbon\Carbon::create($currentYear, $currentMonth)->locale('id')->format('F Y') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="?month={{ max(1, $currentMonth - 1) }}" class="feature-btn px-4 py-2 text-sm" style="background: #6b7280;">
                                <svg class="inline w-4 h-4 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Sebelumnya
                            </a>
                            <a href="?month={{ min(12, $currentMonth + 1) }}" class="feature-btn px-4 py-2 text-sm" style="background: #6b7280;">
                                Selanjutnya
                                <svg class="inline w-4 h-4 -mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                            </a>
                        </div>
                    </div>
                    <!-- Month Pills -->
                    <div class="flex flex-wrap gap-2" style="overflow-x: auto; padding-bottom: 8px;">
                        @for($m = 1; $m <= 12; $m++)
                            <a href="?month={{ $m }}" class="px-3 py-2 text-sm font-medium rounded-full {{ $currentMonth == $m ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200' }} transition-all whitespace-nowrap">
                                {{ \Carbon\Carbon::create($currentYear, $m)->locale('id')->monthName }}
                            </a>
                        @endfor
                    </div>
                </div>
            </section>

<!-- Monthly Statistics -->
            <section class="stats-section mb-8">
                <!-- Working Days Info -->
                <div class="stat-card mb-6" style="background: linear-gradient(135deg, #dbeafe, #eff6ff); border-left: 4px solid #3b82f6;">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="stat-title" style="color: #1e40af;">📅 Hari Kerja dalam Bulan</div>
                            <div class="stat-value" style="font-size: 28px;">{{ $workingDays ?? 0 }} hari</div>
                        </div>
                        <div class="text-right" style="color: #64748b;">
                            <div>{{ \Carbon\Carbon::create($currentYear, $currentMonth)->locale('id')->format('F Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon success">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Hadir</div>
                        </div>
                        <div class="stat-value">{{ $totalHadir }}</div>
                        <div class="stat-description">{{ isset($workingDays) && $workingDays > 0 ? number_format(($totalHadir / $students->count()) * 100, 1) . '% dari semua siswa' : '0%' }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon warning">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Sakit</div>
                        </div>
                        <div class="stat-value">{{ $totalSakit }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon info">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Izin</div>
                        </div>
                        <div class="stat-value">{{ $totalIzin }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon danger">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Alpha</div>
                        </div>
                        <div class="stat-value">{{ $totalAlpha }}</div>
                    </div>
                </div>
            </section>

            <!-- Students Summary Table -->
            <section class="tables-section">
                <div class="table-card">
                    <div class="table-header">
                        <h2 class="table-title">Ringkasan Absensi per Siswa</h2>
                        <p class="stat-description">Klik 'Lihat Detail' untuk melihat absensi harian siswa</p>
                    </div>
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-center">Sakit</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Alpha</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php
                                            $studentAttendances = $attendances->get($student->id, collect());
                                            $hadir = $studentAttendances->where('status', 'hadir')->count();
                                            $sakit = $studentAttendances->where('status', 'sakit')->count();
                                            $izin = $studentAttendances->where('status', 'izin')->count();
                                            $alpha = $studentAttendances->where('status', 'alpha')->count();
                                            $total = $studentAttendances->count();
                                        @endphp
                                        <tr>
                                            <td class="font-semibold">{{ $student->name }}</td>
                                            <td class="text-center"><span class="status-badge success px-3 py-1">{{ $hadir }}</span></td>
                                            <td class="text-center"><span class="status-badge warning px-3 py-1">{{ $sakit }}</span></td>
                                            <td class="text-center"><span class="status-badge info px-3 py-1">{{ $izin }}</span></td>
                                            <td class="text-center"><span class="status-badge danger px-3 py-1">{{ $alpha }}</span></td>
                                            <td class="text-center">
                                                <span class="font-bold text-lg px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full text-blue-800">{{ $total }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($total > 0)
                                                    <button onclick="showDetail({{ $student->id }})" class="feature-btn text-sm px-4 py-2" style="background: #3b82f6; min-width: 100px;">Lihat Detail</button>
                                                @else
                                                    <span class="status-badge secondary px-3 py-1">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Enhanced Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl max-h-[90vh] w-full mx-4">
        <div class="p-8 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="greeting-title" id="modalTitle" style="font-size: 24px;">Detail Absensi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold" style="line-height: 1;">&times;</button>
            </div>
        </div>
        <div id="modalContent" class="p-8 overflow-y-auto max-h-[60vh]">
            <!-- Dynamic content -->
        </div>
    </div>
</div>

<script>
// Original JS preserved + enhancements
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

function formatTime(timeString) {
    if (!timeString) return '-';
    return timeString.substring(0, 5);
}

function showDetail(studentId) {
    const modal = document.getElementById('detailModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Student names from PHP
    const studentNames = @json(collect($students->pluck('name', 'id')->toArray()));
    
    modalTitle.textContent = `Detail Absensi - ${studentNames[studentId]} (${{ \Carbon\Carbon::create($currentYear, $currentMonth)->locale('id')->monthName }} ${{ $currentYear }})`;
    
    fetch(`/sekretaris/api/student-attendance/${studentId}?month={{ $currentMonth }}&year={{ $currentYear }}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                            </tr>
                        </thead>
                        <tbody>`;

            data.forEach(attendance => {
                const statusClass = {
                    'hadir': 'success',
                    'sakit': 'warning', 
                    'izin': 'info',
                    'alpha': 'danger',
                    'belum_absen': 'secondary',
                    'libur': 'primary'
                }[attendance.status] || 'secondary';
                
                // Format status label
                let statusLabel = attendance.status.charAt(0).toUpperCase() + attendance.status.slice(1).replace('_', ' ');
                if (attendance.status === 'libur' && attendance.holiday_note) {
                    statusLabel = `📅 ${attendance.holiday_note}`;
                } else if (attendance.status === 'libur') {
                    statusLabel = '📅 Hari Libur';
                }
                
                html += `
                    <tr>
                        <td class="font-medium">${formatDate(attendance.date)}</td>
                        <td><span class="status-badge ${statusClass} px-3 py-1">${statusLabel}</span></td>
                        <td>${formatTime(attendance.attendance_time)}</td>
                    </tr>`;
            });
            
            html += '</tbody></table></div>';
            modalContent.innerHTML = html;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = '<div class="text-center py-12 text-red-500"><svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Error loading data</div>';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
}

function closeModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close on overlay click
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<!-- Full CSS from dashboard/absensi -->
<style>
/* Identical CSS block as absensi.blade.php - pasted for completeness */
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
.greeting-section, .stats-section, .tables-section { margin-bottom: 32px; }
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; }
.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; text-align: center; }
.stat-header { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 16px; }
.stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }
.stat-icon.success { background: #dcfce7; color: #10b981; }
.stat-icon.warning { background: #fef3c7; color: #f59e0b; }
.stat-icon.info { background: #dbeafe; color: #3b82f6; }
.stat-icon.danger { background: #fee2e2; color: #ef4444; }
.stat-icon svg { width: 24px; height: 24px; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; }
.stat-value { font-size: 40px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.stat-description { font-size: 14px; color: #64748b; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }
.table-title { font-size: 20px; font-weight: 600; color: #1e293b; margin: 0 0 4px 0; }
.table-container { padding: 0; }
.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th { background: #f8fafc; color: #475569; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
.data-table td { padding: 20px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.data-table tbody tr:nth-child(even) { background: #fafbfc; }
.data-table tbody tr:hover { background: #eff6ff; }
.status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; display: inline-block; transition: all 0.2s ease; }
.status-badge.success { background: #dcfce7; color: #166534; border-color: #22c55e; }
.status-badge.warning { background: #fef3c7; color: #92400e; border-color: #eab308; }
.status-badge.info { background: #dbeafe; color: #1e40af; border-color: #3b82f6; }
.status-badge.danger { background: #fee2e2; color: #991b1b; border-color: #ef4444; }
.status-badge.secondary { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.status-badge.primary { background: #e0e7ff; color: #3730a3; border-color: #6366f1; }
.feature-btn { display: inline-flex; align-items: center; gap: 8px; background: #3b82f6; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; }
.feature-btn:hover { background: #2563eb; transform: translateY(-1px); }
#detailModal { transition: opacity 0.3s ease; }
@media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { padding: 20px; } .stats-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endsection

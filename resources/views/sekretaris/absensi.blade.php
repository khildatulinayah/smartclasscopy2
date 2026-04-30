@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.sekretaris-sidebar')

    <div class="main-area">
        <main class="main-content">
            @if(session('success'))
                <div class="stat-card mb-6" style="background: #dcfce7; border-left: 4px solid #10b981;">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #10b98133; color: #059669;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="stat-title" style="color: #166534;">Berhasil Diperbarui</div>
                    </div>
                    <div class="stat-value" style="color: #166534;">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Header Section -->
            <section class="greeting-section mb-8">
                <div class="greeting-card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="greeting-title">Absensi Harian</h1>
                            <p class="greeting-subtitle">Update kehadiran siswa untuk {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->format('l, d F Y') }}</p>
                        </div>
                        <a href="{{ route('sekretaris.dashboard') }}" class="feature-btn" style="background: #6b7280; padding: 8px 16px; font-size: 14px;">
                            ← Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </section>

            <!-- Date Navigation -->
            <section class="feature-cards mb-8" style="grid-template-columns: 1fr auto 1fr; gap: 12px;">
                <a href="{{ route('sekretaris.absensi', ['date' => \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d')]) }}" class="feature-card text-center" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
                    <svg class="feature-icon" style="width: 48px; height: 48px; margin: 0 auto 8px; background: transparent; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <h3 class="feature-title" style="font-size: 16px;">Kemarin</h3>
                </a>
                <a href="{{ route('sekretaris.absensi') }}" class="feature-card text-center" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
                    <div class="feature-icon" style="background: rgba(255,255,255,0.2); color: white; width: 48px; height: 48px; margin: 0 auto 8px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="feature-title" style="color: white; font-size: 18px;">Hari Ini</h3>
                </a>
                <a href="{{ route('sekretaris.absensi', ['date' => \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d')]) }}" class="feature-card text-center" style="background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
                    <svg class="feature-icon" style="width: 48px; height: 48px; margin: 0 auto 8px; background: transparent; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19l-7-7 7-7"></path></svg>
                    <h3 class="feature-title" style="font-size: 16px;">Besok</h3>
                </a>
            </section>

            @if($holiday)
                <!-- Holiday Notice -->
                <section class="stats-section mb-8">
                    <div class="stat-card" style="border-left: 5px solid #ef4444; background: linear-gradient(135deg, #fee2e2, #fecaca);">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: #fecaca; color: #dc2626;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">📅 Hari Libur / Hari Merah</div>
                        </div>
                        <div class="stat-value">{{ $holiday->note }}</div>
                        <div class="stat-description">Dibuat oleh: {{ $holiday->creator->name ?? 'Sistem' }}</div>
                        <div class="progress-container" style="margin-top: 16px;">
                            <div class="flex gap-2 justify-end">
                                <form action="{{ route('sekretaris.absensi.delete_holiday') }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus keterangan libur untuk {{ $selectedDate }}?')">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                                    <button type="submit" class="logout-btn" style="width: auto; padding: 8px 16px; background: #ef4444; color: white;">Hapus Libur</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Holiday Message Card -->
                <section class="stats-section mb-8">
                    <div class="stat-card text-center py-12" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                        <div class="text-5xl mb-4">📅</div>
                        <h2 class="greeting-title mb-2" style="font-size: 24px;">Hari Libur - Tidak Ada Absensi</h2>
                        <p class="stat-description" style="font-size: 16px;">Siswa libur hari ini sesuai keterangan di atas.</p>
                    </div>
                </section>
            @else
                <!-- Statistics Cards -->
                <section class="stats-section mb-8">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon success"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div class="stat-title">Hadir</div>
                            </div>
                            <div class="stat-value">{{ $attendances->where('status', 'hadir')->count() }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon warning"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div class="stat-title">Sakit</div>
                            </div>
                            <div class="stat-value">{{ $attendances->where('status', 'sakit')->count() }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon info"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div class="stat-title">Izin</div>
                            </div>
                            <div class="stat-value">{{ $attendances->where('status', 'izin')->count() }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon danger"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div class="stat-title">Alpha</div>
                            </div>
                            <div class="stat-value">{{ $attendances->where('status', 'alpha')->count() }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon secondary"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div class="stat-title">Belum Absen</div>
                            </div>
                            <div class="stat-value">{{ $attendances->where('status', 'belum_absen')->count() }}</div>
                        </div>
                    </div>
                </section>

                <!-- Attendance Form Table -->
                <section class="tables-section">
                    <div class="table-card">
                        <div class="table-header">
                            <h2 class="table-title">Update Absensi Siswa</h2>
                            <p class="stat-description" style="margin-top: 4px;">Pilih status untuk setiap siswa dan konfirmasi jam masuk jika ada.</p>
                        </div>
                        <form action="{{ route('sekretaris.absensi.update') }}" method="POST" class="table-container">
                            @csrf
                            <input type="hidden" name="date" value="{{ $selectedDate }}">
                            <div class="overflow-x-auto">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Status Absensi</th>
                                            <th>Jam Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            @php
                                                $attendance = $attendances->get($student->id);
                                                $status = $attendance ? $attendance->status : 'belum_absen';
                                                $time = $attendance ? $attendance->attendance_time : '-';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="font-medium">{{ $student->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="flex flex-wrap gap-1 justify-center">
                                                        <label class="cursor-pointer status-toggle">
                                                            <input type="radio" name="status[{{ $student->id }}]" value="belum_absen" {{ $status == 'belum_absen' ? 'checked' : '' }} class="peer sr-only">
                                                            <span class="status-badge secondary peer-checked:!bg-gray-600 peer-checked:!text-white peer-checked:!border-gray-600 transition-all">Belum</span>
                                                        </label>
                                                        <label class="cursor-pointer status-toggle">
                                                            <input type="radio" name="status[{{ $student->id }}]" value="hadir" {{ $status == 'hadir' ? 'checked' : '' }} class="peer sr-only">
                                                            <span class="status-badge success peer-checked:!bg-green-600 peer-checked:!text-white peer-checked:!border-green-600 transition-all">Hadir</span>
                                                        </label>
                                                        <label class="cursor-pointer status-toggle">
                                                            <input type="radio" name="status[{{ $student->id }}]" value="sakit" {{ $status == 'sakit' ? 'checked' : '' }} class="peer sr-only">
                                                            <span class="status-badge warning peer-checked:!bg-yellow-500 peer-checked:!text-white peer-checked:!border-yellow-500 transition-all">Sakit</span>
                                                        </label>
                                                        <label class="cursor-pointer status-toggle">
                                                            <input type="radio" name="status[{{ $student->id }}]" value="izin" {{ $status == 'izin' ? 'checked' : '' }} class="peer sr-only">
                                                            <span class="status-badge info peer-checked:!bg-blue-600 peer-checked:!text-white peer-checked:!border-blue-600 transition-all">Izin</span>
                                                        </label>
                                                        <label class="cursor-pointer status-toggle">
                                                            <input type="radio" name="status[{{ $student->id }}]" value="alpha" {{ $status == 'alpha' ? 'checked' : '' }} class="peer sr-only">
                                                            <span class="status-badge danger peer-checked:!bg-red-600 peer-checked:!text-white peer-checked:!border-red-600 transition-all">Alpha</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="status-badge secondary px-3 py-1">{{ $time != '-' ? \Carbon\Carbon::parse($time)->format('H:i') : '-' }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex flex-col lg:flex-row gap-4 justify-end items-end mt-8 p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                                <div class="flex-1 lg:w-auto">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Hari Libur (opsional)</label>
                                    <textarea name="holiday_note" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical" placeholder="Tulis keterangan jika hari ini libur/merah (kosongkan jika tidak)"></textarea>
                                </div>
                                <button type="submit" class="feature-btn lg:self-end" style="background: #10b981; min-width: 200px;">
                                    💾 Simpan Update Absensi
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            @endif
        </main>
    </div>
</div>

<style>
/* Full dashboard styles copied from sekretaris/dashboard.blade.php */
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
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.feature-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }
.feature-card { background: white; padding: 24px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; text-align: center; transition: all 0.2s ease; text-decoration: none; color: inherit; }
.feature-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.feature-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.feature-icon.blue { background: #dbeafe; color: #3b82f6; }
.feature-icon.green { background: #dcfce7; color: #10b981; }
.feature-icon.orange { background: #fed7aa; color: #f97316; }
.feature-title { font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
.feature-description { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
.feature-btn { display: inline-block; background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
.feature-btn:hover { background: #2563eb; }
.stats-section { margin-bottom: 32px; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; }
.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.stat-icon.success { background: #dcfce7; color: #10b981; }
.stat-icon.warning { background: #fef3c7; color: #f59e0b; }
.stat-icon.info { background: #dbeafe; color: #3b82f6; }
.stat-icon.danger { background: #fee2e2; color: #ef4444; }
.stat-icon.secondary { background: #f3f4f6; color: #6b7280; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; }
.stat-value { font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.stat-description { font-size: 14px; color: #64748b; }
.tables-section { margin-bottom: 32px; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }
.table-title { font-size: 20px; font-weight: 600; color: #1e293b; margin: 0; }
.table-container { padding: 0; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; color: #475569; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
.data-table td { padding: 20px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.data-table tbody tr:nth-child(even) { background: #fafbfc; }
.data-table tbody tr:hover { background: #eff6ff; }
.status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid; white-space: nowrap; transition: all 0.2s ease; }
.status-badge.success { background: #dcfce7; color: #166534; border-color: #22c55e; }
.status-badge.warning { background: #fef3c7; color: #92400e; border-color: #eab308; }
.status-badge.info { background: #dbeafe; color: #1e40af; border-color: #3b82f6; }
.status-badge.danger { background: #fee2e2; color: #991b1b; border-color: #ef4444; }
.status-badge.secondary { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.status-toggle { display: inline-flex; }
.status-toggle input:checked + .status-badge { box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
.progress-container { margin: 16px 0; }
.progress-bar { width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; margin-bottom: 8px; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #10b981, #059669); border-radius: 4px; transition: width 0.3s ease; }
.progress-text { text-align: center; font-size: 18px; font-weight: 700; color: #1e293b; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 100%; position: absolute; z-index: 50; transform: translateX(-100%); } .main-content { padding: 20px; } .stats-grid { grid-template-columns: repeat(2, 1fr); } .feature-cards { grid-template-columns: 1fr; } .lg\:flex-row { flex-direction: column; } }
</style>
@endsection


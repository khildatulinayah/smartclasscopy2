@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="dashboard-layout">
    @include('components.sekretaris-sidebar')

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Laporan Absensi Siswa</h1>
                    <p class="greeting-subtitle">Generate dan cetak laporan absensi bulanan siswa</p>
                </div>
            </section>

            <!-- Report Generation Card -->
            <section class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-12">
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-purple-500 to-indigo-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Laporan Absensi Bulanan</h2>
                            <p class="text-gray-600">Laporan lengkap kehadiran siswa per bulan dengan statistik detail</p>
                        </div>
                    </div>

                    <form id="laporan-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bulan & Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="month" id="laporan-month" class="form-select" required>
                                    <option value="">Pilih Bulan</option>
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="laporan-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakLaporan()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"></path>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="downloadLaporanPDF()" class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Features & Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Features Card -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-100 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Fitur Laporan</h3>
                            <p class="text-gray-600">Laporan absensi lengkap dan profesional</p>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Statistik Lengkap</div>
                                <div class="text-sm text-gray-600">Total hadir, sakit, izin, alpa per bulan</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Detail Per Siswa</div>
                                <div class="text-sm text-gray-600">Tabel kehadiran per siswa per hari</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Exclude Weekend</div>
                                <div class="text-sm text-gray-600">Otomatis exclude Sabtu/Minggu</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Hari Libur</div>
                                <div class="text-sm text-gray-600">Informasi hari libur dan keterangan</div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- How to use Card -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-100 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Cara Penggunaan</h3>
                            <p class="text-gray-600">Langkah mudah membuat laporan</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-sm">1</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Pilih Periode</div>
                                <div class="text-sm text-gray-600">Pilih bulan dan tahun laporan</div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Pilih Format</div>
                                <div class="text-sm text-gray-600">Cetak (HTML) atau PDF</div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-sm">3</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Generate Laporan</div>
                                <div class="text-sm text-gray-600">Laporan otomatis terbuka di tab baru</div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-sm">4</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Download/Print</div>
                                <div class="text-sm text-gray-600">PDF bisa di-download atau dicetak</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function cetakLaporan() {
    const month = document.getElementById('laporan-month').value;
    const year = document.getElementById('laporan-year').value;
    if (month && year) {
        window.open(`{{ route('sekretaris.laporan.cetak', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadLaporanPDF() {
    const month = document.getElementById('laporan-month').value;
    const year = document.getElementById('laporan-year').value;
    if (month && year) {
        window.open(`{{ route('sekretaris.laporan.pdf', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}
</script>
@endsection

<!-- Dashboard CSS -->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', sans-serif; }
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
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.report-card { backdrop-filter: blur(10px); }
.form-select { @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 bg-white shadow-sm hover:shadow-md; background-image: linear-gradient(45deg, transparent 50%, #e5e7eb 0), linear-gradient(135deg, #e5e7eb 50%, transparent 0); background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px); background-size: 5px 5px, 5px 5px; background-repeat: no-repeat; appearance: none; }
.main-content h1, .main-content h2 { font-weight: 800; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
</style>

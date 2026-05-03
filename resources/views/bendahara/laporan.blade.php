@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    @include('components.bendahara-sidebar')

    <div class="main-area">
        <main class="main-content">
            <!-- Header -->
            <section class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Cetak Laporan Keuangan</h1>
                        <p class="text-lg text-gray-600">Pilih periode untuk mencetak laporan riwayat kas dan pembayaran siswa</p>
                    </div>
                </div>
            </section>

            <!-- Report Cards -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Card 1: Riwayat Keluar Masuk Uang -->
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-blue-500 to-indigo-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Riwayat Keluar Masuk Uang</h2>
                            <p class="text-gray-600">Laporan lengkap transaksi kas (pemasukan & pengeluaran)</p>
                        </div>
                    </div>

                    <form id="keuangan-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bulan & Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="month" id="keuangan-month" class="form-select" required>
                                    <option value="">Pilih Bulan</option>
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="keuangan-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakKeuangan()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"></path>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="downloadKeuanganPDF()" class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 2: Pembayaran Siswa Mingguan -->
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-green-500 to-emerald-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Riwayat Pembayaran Siswa</h2>
                            <p class="text-gray-600">Laporan pembayaran mingguan siswa per bulan</p>
                        </div>
                    </div>

                    <form id="pembayaran-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bulan & Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="month" id="pembayaran-month" class="form-select" required>
                                    <option value="">Pilih Bulan</option>
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <select name="year" id="pembayaran-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakPembayaran()" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"></path>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="downloadPembayaranPDF()" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Features & How to use -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Features Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-100 rounded-2xl p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Fitur Laporan</h3>
                            <p class="text-gray-600">Laporan lengkap dan profesional</p>
                        </div>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Format Cetak</div>
                                <div class="text-sm text-gray-600">Printer-friendly dengan layout profesional</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Preview PDF</div>
                                <div class="text-sm text-gray-600">Tampilkan di browser, lalu download</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Data Lengkap</div>
                                <div class="text-sm text-gray-600">Ringkasan dan detail transaksi</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-800">Formal & Resmi</div>
                                <div class="text-sm text-gray-600">Format laporan standar institusi</div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- How to use Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-8">
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
                                <div class="text-sm text-gray-600">Pilih bulan dan tahun yang diinginkan</div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 font-bold text-sm">2</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Pilih Format</div>
                                <div class="text-sm text-gray-600">Cetak (HTML) untuk printer, PDF untuk preview & download</div>
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
                                <div class="font-semibold text-gray-800">Preview & Download</div>
                                <div class="text-sm text-gray-600">PDF muncul di browser, lalu download</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Keuangan Functions
function cetakKeuangan() {
    const month = document.getElementById('keuangan-month').value;
    const year = document.getElementById('keuangan-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.cetak.keuangan', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadKeuanganPDF() {
    const month = document.getElementById('keuangan-month').value;
    const year = document.getElementById('keuangan-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.laporan.pdf', ['month' => ':month', 'year' => ':year', 'type' => 'keuangan']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

// Pembayaran Functions
function cetakPembayaran() {
    const month = document.getElementById('pembayaran-month').value;
    const year = document.getElementById('pembayaran-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.cetak.pembayaran.siswa', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadPembayaranPDF() {
    const month = document.getElementById('pembayaran-month').value;
    const year = document.getElementById('pembayaran-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.laporan.pdf', ['month' => ':month', 'year' => ':year', 'type' => 'pembayaran']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}
</script>

{{-- Dashboard Styles --}}
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: #ffffff; border-right: 1px solid #e5e7eb; box-shadow: 4px 0 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; }
/* Sidebar styles same as before */
.sidebar-header { padding: 2rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.logo { display: flex; align-items: center; gap: 0.75rem; }
.logo-img { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; object-fit: cover; }
.logo-text { font-size: 1.25rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6, #1d4ed8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-nav { flex: 1; padding: 1rem 0; }
.nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.25rem; color: #6b7280; text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 0 1rem 1rem 0; margin: 0 0.75rem; position: relative; overflow: hidden; }
.nav-item:hover { background: #f8fafc; color: #3b82f6; transform: translateX(2px); }
.nav-item.active { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #1d4ed8; font-weight: 600; }
.nav-icon { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
.sidebar-section-header { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; color: #9ca3af; text-transform: uppercase; }
.sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid #f3f4f6; }
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.main-content { flex: 1; padding: 2rem; overflow-y: auto; scroll-behavior: smooth; }
.report-card { backdrop-filter: blur(10px); }
.form-select { @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 bg-white shadow-sm hover:shadow-md; background-image: linear-gradient(45deg, transparent 50%, #e5e7eb 0), linear-gradient(135deg, #e5e7eb 50%, transparent 0); background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px); background-size: 5px 5px, 5px 5px; background-repeat: no-repeat; appearance: none; }
.main-content h1, .main-content h2 { font-weight: 800; }
@media (max-width: 1024px) { .grid-cols-2 { grid-template-columns: 1fr; } }
</style>
@endsection

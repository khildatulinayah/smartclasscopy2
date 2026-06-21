@extends('layouts.app')

@section('title', 'Cetak Laporan Keuangan')

@section('content')
<div class="dashboard-layout">
    @include('components.bendahara-sidebar')

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Cetak Laporan Keuangan</h1>
                    <p class="greeting-subtitle">Pilih periode untuk mencetak laporan riwayat kas dan pembayaran siswa</p>
                </div>
            </section>

            <!-- Report Cards -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 items-stretch">
            <!-- Card 3: Laporan Tahunan -->
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-yellow-500 to-amber-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7.5l-8-4-8 4v9l8 4 8-4v-9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Laporan Tahunan</h2>
                            <p class="text-gray-600">Rekap tahunan kas & pembayaran siswa</p>
                        </div>
                    </div>

                    <form id="tahunan-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="year" id="tahunan-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakTahunan()" class="bg-gradient-to-r from-yellow-600 to-amber-600 hover:from-yellow-700 hover:to-amber-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                </svg>
                                Cetak
                            </button>

                            <button type="button" onclick="downloadTahunanPDF()" class="bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview PDF
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 1: Riwayat Keluar Masuk Uang -->
                <!-- (diletakkan ulang agar card 3 tampil sebagai item ke-3) -->
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview PDF
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview PDF
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card 4: Daftar Tunggakan -->
                <div class="report-card bg-white rounded-2xl shadow-xl border border-gray-100 p-8 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="report-icon bg-gradient-to-br from-red-500 to-rose-600 p-4 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Daftar Tunggakan</h2>
                            <p class="text-gray-600">Rekap tunggakan siswa per tahun (Jan - bulan berjalan)</p>
                        </div>
                    </div>

                    <form id="tunggakan-form" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Tahun</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <select name="year" id="tunggakan-year" class="form-select" required>
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" onclick="cetakTunggakan()" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"></path>
                                </svg>
                                Cetak
                            </button>
                            <button type="button" onclick="downloadTunggakanPDF()" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview PDF
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
                                <div class="text-sm text-gray-600">Cetak (HTML) untuk printer, Preview PDF untuk lihat di browser</div>
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
                                <div class="text-sm text-gray-600">PDF tampil di browser, bisa download dari browser</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
            // Laporan Tahunan Functions
function cetakTahunan() {
    const year = document.getElementById('tahunan-year').value;
    if (year) {
        window.open(`{{ route('bendahara.cetak.keuangan.tahunan', ['year' => ':year']) }}`.replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih tahun terlebih dahulu');
    }
}

function downloadTahunanPDF() {
    const year = document.getElementById('tahunan-year').value;
    if (year) {
        // Arahkan ke Blade "laporan-keuangan-tahunan-perbulan-cetak" (preview PDF)
window.open(`{{ route('bendahara.laporan.pdf.keuangan.tahunan', ['year' => ':year']) }}`.replace(':year', year), '_blank');
        // Pastikan endpoint tahunan menggunakan template laporan-keuangan-tahunan-perbulan-cetak.php
        // (bukan template laporan-keuangan-cetak.php).
        
    } else {
        showWarningToast('Silakan pilih tahun terlebih dahulu');
    }
}

            // Keuangan Functions
function cetakKeuangan() {
    const month = document.getElementById('keuangan-month').value;
    const year = document.getElementById('keuangan-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.cetak.keuangan', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadKeuanganPDF() {
    const month = document.getElementById('keuangan-month').value;
    const year = document.getElementById('keuangan-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.laporan.pdf.keuangan', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

// Pembayaran Functions
function cetakPembayaran() {
    const month = document.getElementById('pembayaran-month').value;
    const year = document.getElementById('pembayaran-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.cetak.pembayaran.siswa', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function downloadPembayaranPDF() {
    const month = document.getElementById('pembayaran-month').value;
    const year = document.getElementById('pembayaran-year').value;
    if (month && year) {
        window.open(`{{ route('bendahara.laporan.pdf.pembayaran', ['month' => ':month', 'year' => ':year']) }}`.replace(':month', month).replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih bulan dan tahun terlebih dahulu');
    }
}

function cetakTunggakan() {
    const year = document.getElementById('tunggakan-year').value;
    if (year) {
        window.open(`{{ route('bendahara.cetak.tunggakan', ['year' => ':year']) }}`.replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih tahun terlebih dahulu');
    }
}

function downloadTunggakanPDF() {
    const year = document.getElementById('tunggakan-year').value;
    if (year) {
        window.open(`{{ route('bendahara.laporan.pdf.tunggakan', ['year' => ':year']) }}`.replace(':year', year), '_blank');
    } else {
        showWarningToast('Silakan pilih tahun terlebih dahulu');
    }
}

// Fungsi untuk menampilkan toast warning
function showWarningToast(message) {
    // Buat elemen toast jika belum ada
    let toast = document.getElementById('warningToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'warningToast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f59e0b;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 9999;
            font-weight: 500;
            transition: all 0.3s ease;
            transform: translateX(100%);
        `;
        document.body.appendChild(toast);
    }
    
    toast.textContent = message;
    toast.style.transform = 'translateX(0)';
    
    // Sembunyikan toast setelah 3 detik
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
    }, 3000);
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
.nav-btn { display: flex; align-items: center; gap: 8px; background: #3b82f6; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
.nav-btn:hover { background: #2563eb; transform: translateY(-1px); }
.action-btn { display: inline-flex; align-items: center; gap: 4px; background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.action-btn:hover { background: #059669; transform: translateY(-1px); }
.report-card { backdrop-filter: blur(10px); }
.form-select { @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-3 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 bg-white shadow-sm hover:shadow-md; background-image: linear-gradient(45deg, transparent 50%, #e5e7eb 0), linear-gradient(135deg, #e5e7eb 50%, transparent 0); background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px); background-size: 5px 5px, 5px 5px; background-repeat: no-repeat; appearance: none; }
.main-content h1, .main-content h2 { font-weight: 800; }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
</style>

        </main>
    </div>
</div>
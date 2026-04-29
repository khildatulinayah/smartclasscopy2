@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl text-center font-bold mb-6">Dashboard Siswa</h1>
    <p class="text-gray-600 mb-8 text-2xl">Selamat datang, {{ auth()->user()->name }}!</p>

    <!-- Status Hari Ini -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Status Hari Ini</h2>
        </div>
        <div class="p-6">
            <div class="text-center">
                <div class="text-4xl mb-2">
                    {{ $statusHariIni == 'hadir' ? '✅' : 
                       ($statusHariIni == 'sakit' ? '🤒' : 
                       ($statusHariIni == 'izin' ? '📝' : 
                       ($statusHariIni == 'alpha' ? '❌' : '⏳')) }}
                </div>
                <div class="text-lg font-semibold mb-2">
                    {{ ucfirst($statusHariIni) }}
                </div>
                <div class="text-sm text-gray-600">
                    {{ \Carbon\Carbon::now()->locale('id')->format('l, d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Absensi Bulanan -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Absensi Bulanan</h2>
                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->locale('id')->format('F Y') }}</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $totalHadir }}</div>
                        <div class="text-sm text-gray-600">Hadir</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $totalSakit }}</div>
                        <div class="text-sm text-gray-600">Sakit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalIzin }}</div>
                        <div class="text-sm text-gray-600">Izin</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $totalAlpha }}</div>
                        <div class="text-sm text-gray-600">Alpha</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembayaran Kas -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Pembayaran Kas</h2>
                <p class="text-sm text-gray-600">Maret 2026</p>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium">Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $statusKas == 'Lunas' ? 'bg-green-100 text-green-700' : 
                               ($statusKas == 'Belum Bayar' ? 'bg-red-100 text-red-700' : 
                               'bg-yellow-100 text-yellow-700') }}">
                            {{ $statusKas }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($paidWeeks / $totalWeeks) * 100 }}%"></div>
                    </div>
                    <div class="text-center text-sm text-gray-600 mt-1">
                        {{ $paidWeeks }} dari {{ $totalWeeks }} minggu
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @for($week = 1; $week <= 4; $week++)
                        @php
                            $payment = $weeklyPayments->where('week_number', $week)->first();
                            $isPaid = $payment && $payment->status == 'paid';
                        @endphp
                        <div class="flex justify-between items-center p-3 rounded
                            {{ $isPaid ? 'bg-green-50' : 'bg-red-50' }}">
                            <div class="flex items-center">
                                <span class="mr-3">{{ $isPaid ? '✅' : '❌' }}</span>
                                <div>
                                    <div class="font-medium text-sm">Minggu {{ $week }}</div>
                                    <div class="text-xs text-gray-600">Rp 5.000</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium
                                {{ $isPaid ? 'text-green-600' : 'text-red-600' }}">
                                {{ $isPaid ? 'Lunas' : 'Belum' }}
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Records -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Recent Attendance -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Riwayat Absensi Terbaru</h2>
            </div>
            <div class="p-4">
                <div class="space-y-2">
                    @forelse($attendances->take(5) as $attendance)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div>
                                <div class="font-medium text-sm">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-600">
                                    {{ $attendance->attendance_time ? 'Jam: ' . \Carbon\Carbon::parse($attendance->attendance_time)->format('H:i') : 'Tidak ada jam' }}
                                </div>
                            </div>
                            <div class="px-2 py-1 rounded text-xs font-medium
                                {{ $attendance->status == 'hadir' ? 'bg-green-100 text-green-700' : 
                                   ($attendance->status == 'sakit' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($attendance->status == 'izin' ? 'bg-blue-100 text-blue-700' : 
                                   ($attendance->status == 'alpha' ? 'bg-red-100 text-red-700' : 
                                   'bg-gray-100 text-gray-700')) }}">
                                {{ ucfirst($attendance->status) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">
                            Belum ada riwayat absensi
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Riwayat Kas Terbaru</h2>
            </div>
            <div class="p-4">
                <div class="space-y-2">
                    @forelse($transactions->take(5) as $transaction)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div>
                                <div class="font-medium text-sm">{{ $transaction->description }}</div>
                                <div class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-sm
                                    {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $transaction->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">
                            Belum ada riwayat transaksi
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <div class="text-green-600">📅</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Total Hari Absen</div>
                    <div class="text-xl font-bold">{{ $totalDays }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <div class="text-blue-600">💰</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Total Kas Bulanan</div>
                    <div class="text-xl font-bold">Rp {{ number_format($totalKasBulanan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <div class="text-purple-600">📊</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Tunggakan Kas</div>
                    <div class="text-xl font-bold text-red-600">Rp {{ number_format($kasTunggakan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

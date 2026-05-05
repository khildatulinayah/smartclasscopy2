@extends('layouts.bendahara')

@section('title', 'Pengaturan Kas')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Kas</h1>
        <p class="text-sm text-gray-500 mt-1">
            Atur nominal kas per bulan. Nominal dipakai saat transaksi baru dibuat untuk bulan tersebut.
        </p>

        @if(session('success'))
            <div class="mt-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if($isCurrentMonth)
            <div class="mt-6 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800 text-sm">
                Mengubah nominal bulan ini tidak mempengaruhi pembayaran yang sudah dilakukan.
            </div>
        @endif

        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3">
            <p class="text-sm text-blue-700">
                Nominal saat ini:
                <span class="font-semibold">Rp {{ number_format($currentNominal, 0, ',', '.') }}</span>
                untuk {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->locale('id')->translatedFormat('F Y') }}
            </p>
        </div>

        <form action="{{ route('bendahara.kas.settings') }}" method="GET" class="mt-6 grid md:grid-cols-2 gap-4">
            <div>
                <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <select id="filter_month" name="month" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->locale('id')->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                <select id="filter_year" name="year" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required>
                    @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                    Tampilkan Bulan
                </button>
            </div>
        </form>

        <form action="{{ route('bendahara.kas.settings.update') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">

            <div>
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal Kas Baru (Rp)</label>
                <input
                    type="number"
                    id="nominal"
                    name="nominal"
                    min="0"
                    step="1"
                    required
                    value="{{ old('nominal', $currentNominal) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: 5000"
                >
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                    Simpan Pengaturan
                </button>
                <a href="{{ route('bendahara.weekly.payments') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

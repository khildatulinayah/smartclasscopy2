@extends('layouts.app')

@section('content')

<div class="bg-white p-6 rounded-lg shadow-md">

    <h2 class="text-xl font-bold mb-4 text-center">Pilih Periode Laporan</h2>
    <p class="text-sm text-gray-600 mb-6 text-center">Absensi Siswa</p>

    <form method="GET" action="{{ route('sekretaris.laporan') }}" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-2">Bulan:</label>
            <select name="bulan" class="w-full p-2 border border-gray-300 rounded-md">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Tahun:</label>
            <input type="number" name="tahun" value="{{ date('Y') }}" 
                   class="w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="text-center">
            <button type="submit" 
                    class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
                Lihat Laporan
            </button>
        </div>
    </form>

</div>

@endsection
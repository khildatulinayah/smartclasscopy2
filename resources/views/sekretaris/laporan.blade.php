@extends('layouts.app')

@section('content')

<div class="bg-white p-6 rounded-lg shadow-md">

    <div class="mb-4 flex gap-4">
        <a href="{{ route('sekretaris.laporan') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            ← Kembali
        </a>

        <a href="{{ route('sekretaris.laporan.cetak', ['bulan'=>$bulan, 'tahun'=>$tahun]) }}" 
           target="_blank"
           class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
            Cetak PDF
        </a>
    </div>

    @php
        $namaBulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
    @endphp

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">Laporan Absensi</h2>
        <p class="text-gray-600">
            {{ $namaBulan[$bulan] }} {{ $tahun }}
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama</th>

                    @for ($i = 1; $i <= $jumlahHari; $i++)
                        <th class="border border-gray-300 px-2 py-2">{{ $i }}</th>
                    @endfor

                    <th class="border border-gray-300 px-4 py-2">H</th>
                    <th class="border border-gray-300 px-4 py-2">S</th>
                    <th class="border border-gray-300 px-4 py-2">I</th>
                    <th class="border border-gray-300 px-4 py-2">A</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($laporan as $index => $row)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $row['nama'] }}</td>

                        @for ($i = 1; $i <= $jumlahHari; $i++)
                            @php $status = $row['hari'][$i]; @endphp

                            <td class="border border-gray-300 px-2 py-2 text-center">
                                @if ($status == 'hadir') 
                                    <span class="text-green-600">H</span>
                                @elseif ($status == 'sakit') 
                                    <span class="text-yellow-600">S</span>
                                @elseif ($status == 'izin') 
                                    <span class="text-blue-600">I</span>
                                @elseif ($status == 'alpha') 
                                    <span class="text-red-600">A</span>
                                @else 
                                    -
                                @endif
                            </td>
                        @endfor

                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $row['total']['hadir'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $row['total']['sakit'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $row['total']['izin'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $row['total']['alpha'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 text-right">
        <p class="text-sm text-gray-600">Mengetahui,</p>
        <br><br>
        <p class="text-sm font-semibold">Sekretaris</p>
    </div>

</div>

@endsection
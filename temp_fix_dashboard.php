<?php
$path = 'resources/views/sekretaris/dashboard.blade.php';
$content = <<<'BLADE'
@extends('layouts.app')

@section('content')
<div style="font-family:Inter,sans-serif;background:#f8fafc;min-height:100vh;padding:24px;">

    <!-- Greeting -->
    <div style="margin-bottom:24px;">
        @php
            $hour = now()->hour;
            $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        @endphp
        <h1 style="font-size:28px;font-weight:700;color:#1e293b;">{{ $greeting }}, {{ auth()->user()->name }}! 👋</h1>
        <p style="color:#64748b;">Kelola absensi siswa dengan mudah</p>
    </div>

    <!-- Feature Cards -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        <a href="{{ route('sekretaris.absensi') }}" style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);text-align:center;text-decoration:none;color:#1e293b;display:block;">
            <div style="font-size:40px;margin-bottom:12px;">📋</div>
            <h3 style="font-size:18px;font-weight:600;margin-bottom:8px;">Absensi</h3>
            <p style="color:#64748b;font-size:14px;">Input absensi harian siswa</p>
        </a>
        <a href="{{ route('sekretaris.tracker') }}" style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);text-align:center;text-decoration:none;color:#1e293b;display:block;">
            <div style="font-size:40px;margin-bottom:12px;">📊</div>
            <h3 style="font-size:18px;font-weight:600;margin-bottom:8px;">Rekap Absensi</h3>
            <p style="color:#64748b;font-size:14px;">Lihat ringkasan bulanan</p>
        </a>
        <a href="{{ route('sekretaris.laporan') }}" style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);text-align:center;text-decoration:none;color:#1e293b;display:block;">
            <div style="font-size:40px;margin-bottom:12px;">📄</div>
            <h3 style="font-size:18px;font-weight:600;margin-bottom:8px;">Laporan</h3>
            <p style="color:#64748b;font-size:14px;">Cetak laporan absensi</p>
        </a>
    </div>

    <!-- Statistik -->
    <div style="background:#fff;padding:20px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="font-size:18px;font-weight:600;color:#1e293b;">Statistik Hari Ini - {{ \Carbon\Carbon::parse($today)->locale('id')->format('d F Y') }}</h2>
            @if($holiday)
                <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:6px;font-size:13px;">📅 Libur: {{ $holiday->note }}</span>
            @endif
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
            <div style="background:#f8fafc;padding:16px;border-radius:8px;text-align:center;">
                <div style="font-size:28px;font-weight:700;color:#1e293b;">{{ $stats['total'] }}</div>
                <div style="font-size:13px;color:#64748b;">Total Siswa</div>
            <div style="background:#dcfce7;padding:16px;border-radius:8px;text-align:center;">
                <div style="font-size:28px;font-weight:700;color:#166534;">{{ $stats['hadir'] }}</div>
                <div style="font-size:13px;color:#166534;">Hadir</div>
            <div style="background:#fef3c7;padding:16px;border-radius:8px;text-align:center;">
                <div style="font-size:28px;font-weight:700;color:#92400e;">{{ $stats['izin'] }}</div>
                <div style="font-size:13px;color:#92400e;">Izin</div>
            <div style="background:#fee2e2;padding:16px;border-radius:8px;text-align:center;">
                <div style="font-size:28px;font-weight:700;color:#991b1b;">{{ $stats['alpha'] }}</div>
                <div style="font-size:13px;color:#991b1b;">Alpa</div>
        </div>

    <!-- Tabel Absensi -->
    <div style="background:#fff;padding:20px;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="font-size:18px;font-weight:600;color:#1e293b;">Absensi Harian</h2>
            <form action="{{ route('sekretaris.absensi') }}" method="GET" style="display:inline;">
                <input type="date" name="date" value="{{ $today }}" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:14px;">
            </form>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px;text-align:left;font-size:14px;color:#475569;border-bottom:1px solid #e2e8f0;">Nama</th>
                    <th style="padding:12px;text-align:left;font-size:14px;color:#475569;border-bottom:1px solid #e2e8f0;">Kelas</th>
                    <th style="padding:12px;text-align:center;font-size:14px;color:#475569;border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="padding:12px;text-align:center;font-size:14px;color:#475569;border-bottom:1px solid #e2e8f0;">Jam Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAttendances as $item)
                    @php
                        $statusConfig = [
                            'hadir' => ['label' => 'Hadir', 'bg' => '#dcfce7', 'color' => '#166534'],
                            'sakit' => ['label' => 'Sakit', 'bg' => '#fef3c7', 'color' => '#92400e'],
                            'izin' => ['label' => 'Izin', 'bg' => '#dbeafe', 'color' => '#1e40af'],
                            'alpha' => ['label' => 'Alpa', 'bg' => '#fee2e2', 'color' => '#991b1b'],
                            'belum_absen' => ['label' => 'Belum Absen', 'bg' => '#f1f5f9', 'color' => '#475569'],
                        ];
                        $sc = $statusConfig[$item['status']] ?? $statusConfig['belum_absen'];
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:12px;font-size:14px;font-weight:600;color:#1e293b;">{{ $item['student']->name }}</td>
                        <td style="padding:12px;font-size:14px;color:#64748b;">{{ $item['class'] }}</td>
                        <td style="padding:12px;text-align:center;">
                            <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:4px 12px;border-radius:12px;font-size:12px;font-weight:500;">{{ $sc['label'] }}</span>
                        </td>
                        <td style="padding:12px;text-align:center;font-size:14px;color:#64748b;">
                            {{ $item['attendance_time'] ? \Carbon\Carbon::parse($item['attendance_time'])->format('H:i') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:24px;text-align:center;color:#64748b;">Tidak ada data siswa aktif</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($stats['total'] > 10)
            <div style="padding:12px;text-align:center;border-top:1px solid #f1f5f9;">
                <a href="{{ route('sekretaris.absensi') }}" style="color:#3b82f6;text-decoration:none;font-size:14px;font-weight:500;">Lihat Semua {{ $stats['total'] }} Siswa →</a>
            </div>
        @endif
    </div>

    <!-- Navigasi Bawah -->
    <div style="display:flex;gap:12px;">
        <a href="{{ route('sekretaris.absensi') }}" style="flex:1;background:#3b82f6;color:#fff;padding:14px;border-radius:10px;text-align:center;text-decoration:none;font-weight:600;font-size:15px;">📋 Mulai Absensi</a>
        <a href="{{ route('sekretaris.tracker') }}" style="flex:1;background:#10b981;color:#fff;padding:14px;border-radius:10px;text-align:center;text-decoration:none;font-weight:600;font-size:15px;">📊 Lihat Rekap</a>
        <a href="{{ route('sekretaris.laporan') }}" style="flex:1;background:#f59e0b;color:#fff;padding:14px;border-radius:10px;text-align:center;text-decoration:none;font-weight:600;font-size:15px;">📄 Laporan</a>
    </div>
@endsection
BLADE;

file_put_contents($path, $content);
echo "Written " . strlen($content) . " bytes to $path\n";

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Daftar Tunggakan</title>

    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }

        body{
            font-family: DejaVu Sans, sans-serif;
            color:#222;
            font-size:12px;
            line-height:1.45;
        }

        .container{ padding:25px; }

        @page{ margin:1.2cm; size:A4 portrait; }

        /* HEADER */
        .header{ width:100%; margin-bottom:18px; }
        .header-table{ width:100%; border-collapse:collapse; }
        .header-table td{ vertical-align:top; }
        .school-info{ text-align:center; }
        .school-name{
            font-size:22px;
            font-weight:bold;
            text-transform:uppercase;
        }
        .school-address{ font-size:11px; margin-top:4px; }
        .report-title{ margin-top:15px; font-size:18px; font-weight:bold; text-transform:uppercase; }
        .period{ margin-top:5px; font-size:12px; }
        .line{ margin-top:18px; border-top:2px solid #000; border-bottom:1px solid #000; height:4px; }

        /* SUMMARY */
        .summary{ margin:20px 0; }
        .summary-table{ width:100%; border-collapse:collapse; }
        .summary-table td{
            border:1px solid #000;
            padding:12px;
            text-align:center;
        }
        .summary-title{ font-size:11px; margin-bottom:6px; }
        .summary-value{ font-size:16px; font-weight:bold; }

        /* MAIN TABLE */
        .main-table{ width:100%; border-collapse:collapse; margin-top:10px; }
        .main-table th{
            background:#eaeaea;
            border:1px solid #000;
            padding:9px 8px;
            text-align:center;
            font-size:11px;
        }
        .main-table td{
            border:1px solid #000;
            padding:8px;
            font-size:11px;
            vertical-align:top;
        }

        .text-center{ text-align:center; }
        .text-right{ text-align:right; }

        .total-row td{ background:#f3f3f3; font-weight:bold; }

        .muted{ font-style:italic; font-size:10px; color:#444; }

        /* FOOTER */
        .footer{ margin-top:50px; width:100%; }
        .signature{ width:250px; text-align:center; float:right; }
        .signature-space{ height:70px; }
        .signature-name{ font-weight:bold; text-decoration:underline; }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="school-info">
                    <div class="school-name">SMARTCLASS</div>
                    <div class="school-address">Sistem Manajemen Keuangan dan Administrasi Kelas</div>
                    <div class="report-title">Laporan Daftar Tunggakan</div>
                    <div class="period">Periode Januari - {{ $endMonthName }} Tahun {{ $year }}</div>
                </td>
            </tr>
        </table>
        <div class="line"></div>
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td width="34%">
                    <div class="summary-title">TOTAL TUNGGAKAN</div>
                    <div class="summary-value">Rp {{ number_format($totalTunggakan,0,',','.') }}</div>
                </td>
                <td width="33%">
                    <div class="summary-title">JUMLAH SISWA MENUNGGAK</div>
                    <div class="summary-value">{{ $totalSiswaMenunggak }} siswa</div>
                </td>
                <td width="33%">
                    <div class="summary-title">JUMLAH MINGGU TERTUNGGAK</div>
                    <div class="summary-value">{{ $totalMingguTertunggak }} minggu</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:25%;">Nama Siswa</th>
                <th style="width:10%;">Jml Minggu</th>
                <th style="width:40%;">Rincian Minggu Tertunggak</th>
                <th style="width:20%;">Total Tunggakan</th>
            </tr>
        </thead>
        <tbody>

            @forelse($tunggakanPerSiswa as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['student_name'] }}</td>
                    <td class="text-center">{{ $row['week_count'] }}</td>
                    <td>
                        @foreach($row['week_details'] as $wd)
                            <div>{{ $wd['label'] }} (Rp {{ number_format($wd['amount'],0,',','.') }})</div>
                        @endforeach
                    </td>
                    <td class="text-right">Rp {{ number_format($row['total_amount'],0,',','.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada tunggakan. Semua pembayaran lunas.</td>
                </tr>
            @endforelse

            @if($tunggakanPerSiswa->count() > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalTunggakan,0,',','.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <div>{{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-top:5px;">Kepala Sekolah</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ auth()->user()->name }}</div>
        </div>
    </div>

</div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Tahunan</title>

    <style>
        *{ margin:0; padding:0; box-sizing:border-box; }

        body{
            font-family: DejaVu Sans, sans-serif;
            color:#222;
            font-size:12px;
            line-height:1.45;
        }

        .container{ padding:24px 28px; }

        @page{ margin:1.1cm; size:A4 landscape; }

        /* HEADER */
        .header{ width:100%; margin-bottom:14px; }
        .header-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
        .header-table td{ vertical-align:top; }
        .school-info{ text-align:center; padding:2px 10px; }
        .school-name{
            font-size:22px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .school-address{ font-size:11px; margin-top:4px; }
        .report-title{ margin-top:10px; font-size:18px; font-weight:700; text-transform:uppercase; }
        .period{ margin-top:4px; font-size:12px; }
        .line{ margin-top:12px; border-top:2px solid #000; border-bottom:1px solid #000; height:4px; }

        /* SUMMARY */
        .summary{ margin:12px 0 10px; }
        .summary-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
        .summary-table td{
            border:1px solid #000;
            padding:10px 8px;
            text-align:center;
            vertical-align:middle;
        }
        .summary-title{ font-size:11px; font-weight:700; margin-bottom:4px; }
        .summary-value{ font-size:16px; font-weight:800; }

        /* SECTION TITLE */
        .section-title{ margin:14px 0 8px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; }

        /* MAIN TABLE */
        .main-table{ width:100%; border-collapse:collapse; table-layout:fixed; }
        .main-table th{
            background:#e9e9e9;
            border:1px solid #000;
            padding:8px 6px;
            text-align:center;
            font-size:11px;
        }
        .main-table td{
            border:1px solid #000;
            padding:7px 6px;
            font-size:11px;
            vertical-align:top;
        }

        .text-center{ text-align:center; }
        .text-right{ text-align:right; }

        .total-row td{ background:#f3f3f3; font-weight:800; }

        .muted{ font-style:italic; color:#222; }
        .nowrap{ white-space:nowrap; }

        /* FOOTER */
        .footer{ margin-top:55px; width:100%; }
        .signature{ width:260px; text-align:center; float:right; }
        .signature-space{ height:70px; }
        .signature-name{ font-weight:700; text-decoration:underline; }
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
                    <div class="report-title">Laporan Keuangan Tahunan</div>
                    <div class="period">
                        Periode Januari - <span class="nowrap">{{ $endMonthName }}</span> Tahun {{ $year }}
                    </div>
                </td>
            </tr>
        </table>
        <div class="line"></div>
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-title">TOTAL UANG MASUK</div>
                    <div class="summary-value">Rp {{ number_format($incomeTotal,0,',','.') }}</div>
                </td>
                <td>
                    <div class="summary-title">TOTAL UANG KELUAR</div>
                    <div class="summary-value">Rp {{ number_format($expenseTotal,0,',','.') }}</div>
                </td>
                <td>
                    <div class="summary-title">SALDO AKHIR</div>
                    <div class="summary-value">Rp {{ number_format($balanceTotal,0,',','.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Rekap per Bulan (Uang Masuk &amp; Uang Keluar)</div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width:20%;">Bulan</th>
                <th style="width:30%;">Uang Masuk</th>
                <th style="width:30%;">Uang Keluar</th>
                <th style="width:20%;">Saldo Bulan</th>
            </tr>
        </thead>
        <tbody>

            @foreach($monthly as $row)
                <tr>
                    <td class="text-center">{{ $row['monthName'] }}</td>
                    <td class="text-right">Rp {{ number_format($row['income'],0,',','.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['expense'],0,',','.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['balance'] ?? 0,0,',','.') }}</td>

                </tr>

                @if(!empty($row['income_details']) && is_array($row['income_details']) && count($row['income_details']) > 0)
                    @foreach($row['income_details'] as $detail)
                        <tr>
                            <td class="text-center">&nbsp;</td>
                            <td>{{ $detail['label'] ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($detail['amount'] ?? 0,0,',','.') }}</td>
                            <td class="text-center">{{ $detail['unit'] ?? 'Total' }}</td>
                        </tr>
                    @endforeach
                @endif

            @endforeach

            <tr class="total-row">
                <td class="text-center">TOTAL</td>
                <td class="text-right">Rp {{ number_format($incomeTotal,0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($expenseTotal,0,',','.') }}</td>
                <td class="text-right">Rp {{ number_format($balanceTotal,0,',','.') }}</td>
            </tr>
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



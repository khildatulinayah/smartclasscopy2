<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            color:#222;
            font-size:12px;
            line-height:1.5;
        }

        .container{
            padding:25px;
        }

        /* HEADER */
        .header{
            width:100%;
            margin-bottom:25px;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
        }

        .header-table td{
            vertical-align:top;
        }

        .logo{
            width:90px;
        }

        .school-info{
            text-align:center;
        }

        .school-name{
            font-size:22px;
            font-weight:bold;
            text-transform:uppercase;
        }

        .school-address{
            font-size:11px;
            margin-top:4px;
        }

        .report-title{
            margin-top:15px;
            font-size:18px;
            font-weight:bold;
            text-transform:uppercase;
        }

        .period{
            margin-top:5px;
            font-size:12px;
        }

        .line{
            margin-top:18px;
            border-top:2px solid #000;
            border-bottom:1px solid #000;
            height:4px;
        }

        /* SUMMARY */
        .summary{
            margin:20px 0;
        }

        .summary-table{
            width:100%;
            border-collapse:collapse;
        }

        .summary-table td{
            border:1px solid #000;
            padding:12px;
        }

        .summary-title{
            font-size:11px;
            margin-bottom:6px;
        }

        .summary-value{
            font-size:16px;
            font-weight:bold;
        }

        /* TABLE */
        .main-table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        .main-table th{
            background:#eaeaea;
            border:1px solid #000;
            padding:10px 8px;
            text-align:center;
            font-size:11px;
        }

        .main-table td{
            border:1px solid #000;
            padding:8px;
            font-size:11px;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .income{
            color:#000;
        }

        .expense{
            color:#000;
        }

        .total-row{
            background:#f3f3f3;
            font-weight:bold;
        }

        /* FOOTER */
        .footer{
            margin-top:60px;
            width:100%;
        }

        .signature{
            width:250px;
            text-align:center;
            float:right;
        }

        .signature-space{
            height:70px;
        }

        .signature-name{
            font-weight:bold;
            text-decoration:underline;
        }

        @page{
            margin:1.2cm;
            size:A4 landscape;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <table class="header-table">
            <tr>


                <td width="70%" class="school-info">
                    <div class="school-name">
                        SMARTCLASS
                    </div>

                    <div class="school-address">
                        Sistem Manajemen Keuangan dan Administrasi Kelas
                    </div>

                    <div class="report-title">
                        Laporan Keuangan Kelas
                    </div>

                    <div class="period">
                        Bulan
                        {{ \Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F Y') }}
                    </div>
                </td>

                <td width="15%"></td>

            </tr>
        </table>

        <div class="line"></div>
    </div>

    <!-- SUMMARY -->
    <div class="summary">

        <table class="summary-table">
            <tr>

                <td width="33%">
                    <div class="summary-title">
                        TOTAL PEMASUKAN
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($income,0,',','.') }}
                    </div>
                </td>

                <td width="33%">
                    <div class="summary-title">
                        TOTAL PENGELUARAN
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($expense,0,',','.') }}
                    </div>
                </td>

                <td width="34%">
                    <div class="summary-title">
                        SALDO AKHIR
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($balance,0,',','.') }}
                    </div>
                </td>

            </tr>
        </table>

    </div>



    <!-- TABLE PENGANTAR: Kas Siswa (dirangkum per minggu) -->
    <div style="margin-top:20px; font-weight:bold; text-transform:uppercase;">A. Tabel Pemasukan</div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="20%">Jenis</th>
                <th width="25%">Nominal</th>
            </tr>
        </thead>
        <tbody>

        @php
            $incomeRows = collect($incomeRows ?? []);
        @endphp

@php
            if ($incomeRows->count() === 0 && isset($transactions)) {
                // Ringkasan pemasukan per minggu (maks 1 baris per minggu)
                $txIncome = collect($transactions)
                    ->where('type', 'income')
                    ->sortBy('date')
                    ->values();

                // Bagi transaksi menjadi 6 minggu (fallback jika week_number tidak tersedia)
                $incomeBuckets = collect();
                $incomeFirstDateByWeek = [];

                $cnt = max(1, $txIncome->count());
                foreach ($txIncome as $i => $tx) {
                    $week = (isset($tx->week_number) && $tx->week_number) ? (int)$tx->week_number : ((int) floor(($i / $cnt) * 6) + 1);
                    $week = max(1, min(6, $week));

                    $incomeBuckets[$week] = ($incomeBuckets[$week] ?? 0) + (float)($tx->amount ?? 0);
                    if (!isset($incomeFirstDateByWeek[$week])) {
                        $incomeFirstDateByWeek[$week] = $tx->date;
                    }
                }

                $weeksRange = range(1, 6);
                $incomeRows = collect($weeksRange)
                    ->map(function($w) use ($incomeBuckets, $incomeFirstDateByWeek) {
                        $date = $incomeFirstDateByWeek[$w] ?? null;
                        $label = $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : ('Minggu ke-' . $w);
                        $amount = (float)($incomeBuckets[$w] ?? 0);

                        return [
                            'label' => $label,
                            'amount' => $amount,
                        ];
                    })
                    ->filter(fn($r) => $r['amount'] != 0)
                    ->values();
            }
        @endphp

        @if($incomeRows->count() > 0)
            @foreach($incomeRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['label'] }}</td>
                    <td>PEMASUKAN DARI KAS SISWA</td>
                    <td class="text-center">Masuk</td>
                    <td class="text-right income">
                        + Rp {{ number_format($row['amount'] ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">Tidak ada pemasukan.</td>
            </tr>
        @endif





        </tbody>
    </table>

    <div style="margin-top:20px; font-weight:bold; text-transform:uppercase;">B. Tabel Pengeluaran</div>

        <!-- TABLE PENGELUARAN -->

        <!-- TABLE PENGELUARAN -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="20%">Jenis</th>
                <th width="25%">Nominal</th>
            </tr>
        </thead>

        <tbody>

        @php
            $expenseRows = collect($expenseRows ?? []);
        @endphp

        @php
            if ($expenseRows->count() === 0 && isset($transactions)) {
                $expenseRows = collect($transactions)
                    ->where('type', 'expense')
                    ->sortBy('date')
                    ->map(function ($tx) {
                        return [
                            'label' => \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y'),
                            'amount' => (float)($tx->amount ?? 0),
                            'description' => $tx->description ?? 'PENGELUARAN',
                            'receipt_path' => $tx->receipt_path ?? null,
                        ];
                    })
                    ->values();
            }
        @endphp

        @if($expenseRows->count() > 0)
            @foreach($expenseRows as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['label'] }}</td>
                    <td>{{ $row['description'] ?? 'PENGELUARAN' }}</td>
                    <td class="text-center">Keluar</td>
                    <td class="text-right expense">
                        - Rp {{ number_format($row['amount'] ?? 0, 0, ',', '.') }}
                        @if(!empty($row['receipt_path']))
                            <div style="margin-top:6px;">
                                <img src="{{ asset('public/' . $row['receipt_path']) }}" alt="Bukti" style="max-height:60px; max-width:160px; object-fit:contain;" />
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">Tidak ada pengeluaran.</td>
            </tr>
        @endif


        <tr class="total-row">
            <td colspan="3" class="text-right">SALDO AKHIR</td>
            <td colspan="2" class="text-right">Rp {{ number_format($balance,0,',','.') }}</td>
        </tr>


        </tbody>

    </table>


    <!-- FOOTER -->
    <div class="footer">

        <div class="signature">

            <div>
                {{ now()->translatedFormat('d F Y') }}
            </div>

            <div style="margin-top:5px;">
                Kepala Sekolah
            </div>

            <div class="signature-space"></div>

            <div class="signature-name">
                {{ auth()->user()->name }}
            </div>

        </div>

    </div>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran Siswa</title>

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
            text-align:center;
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
            margin-top:20px;
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

        .status-paid{
            background:#dbead5;
            padding:4px 8px;
            border-radius:3px;
            font-weight:bold;
            font-size:10px;
        }

        .status-unpaid{
            background:#f4d6d6;
            padding:4px 8px;
            border-radius:3px;
            font-weight:bold;
            font-size:10px;
        }

        .empty{
            color:#999;
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

        /* NO DATA */
        .no-data{
            margin-top:50px;
            text-align:center;
            font-size:13px;
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
                        Sistem Administrasi dan Keuangan Kelas
                    </div>

                    <div class="report-title">
                        Laporan Pembayaran Siswa Mingguan
                    </div>

                    <div class="period">
                        Periode
                        {{ \Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F Y') }}
                    </div>

                </td>

                <td width="15%"></td>

            </tr>
        </table>

        <div class="line"></div>

    </div>

    @if($payments->count() > 0)

    <!-- SUMMARY -->
    <div class="summary">

        <table class="summary-table">
            <tr>

                <td width="25%">
                    <div class="summary-title">
                        TOTAL TAGIHAN
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalBills,0,',','.') }}
                    </div>
                </td>

                <td width="25%">
                    <div class="summary-title">
                        TOTAL DIBAYAR
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalPaid,0,',','.') }}
                    </div>
                </td>

                <td width="25%">
                    <div class="summary-title">
                        TOTAL TUNGGAKAN
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalBills - $totalPaid,0,',','.') }}
                    </div>
                </td>

                <td width="25%">
                    <div class="summary-title">
                        JUMLAH SISWA
                    </div>

                    <div class="summary-value">
                        {{ $paymentsByStudent->count() }}
                    </div>
                </td>

            </tr>
        </table>

    </div>

    <!-- MAIN TABLE -->
    <table class="main-table">

        <thead>

        <tr>

            <th width="5%">No</th>

            <th width="25%">
                Nama Siswa
            </th>

            @php
                $maxWeek = 0;

                foreach($paymentsByStudent as $studentPayments){
                    foreach($studentPayments as $payment){
                        if($payment->week_number > $maxWeek){
                            $maxWeek = $payment->week_number;
                        }
                    }
                }
            @endphp

            @for($week = 1; $week <= $maxWeek; $week++)

                <th width="10%">
                    Minggu {{ $week }}
                </th>

            @endfor

        </tr>

        </thead>

        <tbody>

        @php $rowNumber = 1; @endphp

        @foreach($paymentsByStudent->sortBy(function($studentPayments){
            $student = optional($studentPayments->first())->student;
            return strtolower(trim($student->name ?? ''));
        }) as $studentId => $studentPayments)

            @php

                $student = $studentPayments->first()->student;

                $paymentsByWeek = [];

                foreach($studentPayments as $payment){
                    $paymentsByWeek[$payment->week_number] = $payment;
                }

            @endphp

            <tr>

                <td class="text-center">
                    {{ $rowNumber }}
                </td>

                <td>
                    {{ $student ? $student->name : '-' }}
                </td>

                @for($week = 1; $week <= $maxWeek; $week++)

                    <td class="text-center">

                        @if(isset($paymentsByWeek[$week]))

                            @if($paymentsByWeek[$week]->status == 'paid')

                                <span class="status-paid">
                                    LUNAS
                                </span>

                            @else

                                <span class="status-unpaid">
                                    BELUM
                                </span>

                            @endif

                        @else

                            <span class="empty">
                                -
                            </span>

                        @endif

                    </td>

                @endfor

            </tr>

            @php $rowNumber++; @endphp

        @endforeach

        </tbody>

    </table>

    @else

        <div class="no-data">
            Belum ada data pembayaran siswa.
        </div>

    @endif

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
                {{ $userName ?? 'Administrator' }}
            </div>

        </div>

    </div>

</div>

</body>
</html>
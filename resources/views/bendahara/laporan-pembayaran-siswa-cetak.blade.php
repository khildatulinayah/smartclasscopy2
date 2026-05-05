<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran Siswa - {{ $monthName }}</title>
    <center>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.3; color: #333; background: white; }
        .container { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
        
        /* Header Styles */
        .header { margin-bottom: 30px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .school-info { flex: 1; }
        .school-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .report-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .period-info { display: flex; gap: 20px; margin-bottom: 10px; font-size: 10px; }
        .period-item { display: flex; align-items: center; gap: 5px; }
        .period-label { font-weight: bold; }
        .logo-right { width: 100px; height: 100px; }
        
        /* Summary Cards */
        .summary { display: flex; justify-content: space-between; gap: 15px; margin: 25px 0; }
        .summary-card { flex: 1; background: #f9f9f9; border: 2px solid #000; padding: 15px; text-align: center; }
        .summary-label { font-size: 11px; font-weight: 600; margin-bottom: 5px; }
        .summary-value { font-size: 16px; font-weight: bold; }
        .summary-arrears .summary-value { color: #dc2626; }
        
        /* Students Section */
        .students-section { margin: 30px 0; }
        .student-group { margin-bottom: 30px; page-break-inside: avoid; }
        .student-header { background: #f0f0f0; border: 1px solid #000; border-bottom: none; padding: 12px 15px; }
        .student-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        .student-info { font-size: 10px; color: #666; }
        
        /* Table Styles */
        .table-container { margin: 25px 0; }
        table { width: 100%; border-collapse: collapse; border: 2px solid #000; font-size: 11px; }
        th { background: #f0f0f0; border: 1px solid #000; padding: 8px 10px; text-align: left; font-weight: 600; font-size: 11px; }
        td { border: 1px solid #000; padding: 8px 10px; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', monospace; }
        .text-muted { color: #999; font-style: italic; }
        .status-paid { color: #166534; font-weight: 600; }
        .status-unpaid { color: #dc2626; font-weight: 600; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 600; }
        .status-lunas { background: #d4edda; color: #155724; }
        .status-belum { background: #f8d7da; color: #721c24; }
        
        /* Total Row */
        .total-row { background: #f0f0f0; font-weight: 600; }
        .total-row td { padding: 10px; font-size: 12px; }
        
        /* Signature section */
        .signature-section { margin-top: 50px; text-align: center; }
        .signature-box { display: inline-block; width: 300px; margin: 0 auto; }
        .signature-title { font-weight: bold; margin-bottom: 30px; }
        .signature-line { border-bottom: 1px solid #000; height: 40px; margin-bottom: 5px; }
        .signature-name { font-weight: bold; }
        
        /* No Data */
        .no-data { text-align: center; padding: 60px; color: #6b7280; }
        .no-data-icon { font-size: 4rem; margin-bottom: 20px; }
        .no-data h2 { font-size: 24px; margin-bottom: 10px; }
        .no-data p { font-size: 12px; }
        
        @media print { 
            body { -webkit-print-color-adjust: exact; font-size: 9px; } 
            .container { padding: 15px 10px; }
            table { font-size: 9px; }
            th, td { padding: 6px 8px; font-size: 9px; line-height: 1.2; }
            .summary { gap: 10px; margin: 20px 0; }
            .summary-card { padding: 10px; }
            .summary-value { font-size: 14px; }
            .school-name { font-size: 18px; }
            .report-title { font-size: 16px; }
            .logo-right { width: 80px; height: 80px; }
            .signature-section { margin-top: 30px; }
        }
        @page { 
            margin: 1cm; 
            size: A4 landscape;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-top">
                <div class="school-info">
                    <div class="school-name">SMARTCLASS</div>
                    <div class="report-title">Laporan Pembayaran Siswa Mingguan</div>
                    <div class="period-info">
                        <div class="period-item">
                            <span class="period-label">Bulan:</span>
                            <span>{{ \Carbon\Carbon::create($year, $month)->locale('id')->format('F') }}</span>
                        </div>
                        <div class="period-item">
                            <span class="period-label">Tahun:</span>
                            <span>{{ $year }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-right" onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        @if($payments->count() > 0)
            <div class="summary">
                <div class="summary-card">
                    <div class="summary-label">Total Tagihan</div>
                    <div class="summary-value">Rp {{ number_format($totalBills, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Sudah Dibayar</div>
                    <div class="summary-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card summary-arrears">
                    <div class="summary-label">Tunggakan</div>
                    <div class="summary-value">Rp {{ number_format($totalBills - $totalPaid, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Jumlah Siswa</div>
                    <div class="summary-value">{{ $paymentsByStudent->count() }}</div>
                </div>
            </div>

            <!-- Tabel Pembayaran Horizontal -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="25%">Nama Siswa</th>
                            @php
                                // Get max week number from all payments
                                $maxWeek = 0;
                                foreach($paymentsByStudent as $studentPayments) {
                                    foreach($studentPayments as $payment) {
                                        if($payment->week_number > $maxWeek) {
                                            $maxWeek = $payment->week_number;
                                        }
                                    }
                                }
                            @endphp
                            @for($week = 1; $week <= $maxWeek; $week++)
                                <th width="12%" class="text-center">Minggu {{ $week }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNumber = 1; @endphp
                        @foreach($paymentsByStudent as $studentId => $studentPayments)
                            @php 
                                $student = $studentPayments->first()->student;
                                // Create array of payments indexed by week number
                                $paymentsByWeek = [];
                                foreach($studentPayments as $payment) {
                                    $paymentsByWeek[$payment->week_number] = $payment;
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $rowNumber }}</td>
                                <td>{{ $student ? $student->name : 'Unknown Student' }}</td>
                                @for($week = 1; $week <= $maxWeek; $week++)
                                    <td class="text-center">
                                        @if(isset($paymentsByWeek[$week]))
                                            @if($paymentsByWeek[$week]->status == 'paid')
                                                <span class="status-badge status-lunas">Lunas</span>
                                            @else
                                                <span class="status-badge status-belum">Belum</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                            @php $rowNumber++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <div class="no-data-icon">📋</div>
                <h2>Belum ada data pembayaran</h2>
                <p>Data pembayaran siswa untuk {{ $monthName }} {{ $year }} belum tersedia.</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Mengetahui, Kepala Sekolah SMARTCLASS</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $userName ?? 'System' }}</div>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran Siswa - {{ $monthName }} {{ $year }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.4; color: #000; background: white; font-size: 12px; }
        .container { max-width: 100%; margin: 0 auto; padding: 20px; }
        
        /* Header Sekolah */
        .header-school { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .school-info { text-align: left; flex: 1; }
        .school-logo { text-align: right; flex: 0 0 100px; }
        .logo { width: 80px; height: 80px; }
        .school-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .school-address { font-size: 11px; margin-bottom: 3px; }
        .school-contact { font-size: 11px; }
        
        /* Judul Laporan */
        .report-title { text-align: center; margin: 30px 0; }
        .report-title h1 { font-size: 20px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
        .report-period { font-size: 14px; font-weight: 600; margin-bottom: 5px; }
        .report-info { font-size: 11px; color: #666; }
        
        /* Summary Cards */
        .summary { display: flex; justify-content: space-between; gap: 15px; margin: 25px 0; }
        .summary-card { flex: 1; background: #f9f9f9; border: 1px solid #000; padding: 15px; text-align: center; }
        .summary-label { font-size: 11px; font-weight: 600; margin-bottom: 5px; }
        .summary-value { font-size: 16px; font-weight: bold; }
        .summary-arrears .summary-value { color: #dc2626; }
        
        /* Tabel Pembayaran Global */
        .table-container { margin: 25px 0; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        th { background: #f0f0f0; border: 1px solid #000; padding: 10px 8px; text-align: left; font-weight: 600; font-size: 11px; }
        td { border: 1px solid #000; padding: 8px; font-size: 11px; }
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
        .total-row td { padding: 10px 8px; font-size: 12px; }
        
        /* Student Group */
        .student-section { margin: 30px 0; page-break-inside: avoid; }
        .student-header { background: #f0f0f0; border: 1px solid #000; padding: 12px 15px; margin-bottom: 0; }
        .student-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        .student-info { font-size: 10px; color: #666; }
        
        /* Footer */
        .footer { margin-top: 40px; }
        .signature-section { display: flex; justify-content: space-between; margin-top: 50px; }
        .signature-box { text-align: center; width: 200px; }
        .signature-line { height: 40px; border-bottom: 1px solid #000; margin-bottom: 5px; }
        .signature-title { font-size: 11px; font-weight: 600; margin-bottom: 20px; }
        .signature-name { font-size: 11px; font-weight: 600; }
        .signature-role { font-size: 10px; }
        
        .footer-info { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #000; font-size: 10px; color: #666; }
        
        /* No Data */
        .no-data { text-align: center; padding: 50px; color: #666; }
        .no-data-icon { font-size: 48px; margin-bottom: 20px; }
        .no-data h2 { font-size: 18px; margin-bottom: 10px; }
        .no-data p { font-size: 12px; }
        
        /* Print Styles */
        @media print {
            body { font-size: 10px; }
            .container { padding: 10px; }
            .header-school { margin-bottom: 20px; }
            .report-title { margin: 20px 0; }
            .summary { gap: 10px; margin: 20px 0; }
            .summary-card { padding: 10px; }
            .summary-value { font-size: 14px; }
            th, td { padding: 6px 5px; font-size: 9px; }
            .signature-section { margin-top: 30px; }
            .footer-info { font-size: 9px; }
        }
        
        @page { 
            margin: 1cm; 
            size: A4 portrait;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Sekolah -->
        <div class="header-school">
            <div class="school-info">
                <div class="school-name">SMARTCLASS</div>
                <div class="school-address">Sistem Manajemen Kelas Digital</div>
                <div class="school-contact">Laporan Pembayaran Siswa Resmi</div>
            </div>
            <div class="school-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMARTCLASS" class="logo" onerror="this.style.display='none'">
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="report-title">
            <h1>Laporan Pembayaran Siswa Mingguan</h1>
            <div class="report-period">Periode: {{ $monthName }} {{ $year }}</div>
            <div class="report-info">Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} | Bendahara Kelas</div>
        </div>

        @if($paymentsByStudent->count() > 0)
            <!-- Calculate Summary -->
            @php
                $totalStudents = $paymentsByStudent->count();
                $totalArrears = $totalBills - $totalPaid;
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
                    <div class="summary-value">Rp {{ number_format($totalArrears, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Jumlah Siswa</div>
                    <div class="summary-value">{{ $totalStudents }}</div>
                </div>
            </div>

            <!-- Tabel Pembayaran Horizontal -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="25%">Nama Siswa</th>
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

        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">Mengetahui,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ auth()->user() ? auth()->user()->name : 'System' }}</div>
                    <div class="signature-role">{{ auth()->user() ? ucfirst(auth()->user()->role) : 'Administrator' }}</div>
                </div>
                <div class="signature-box" style="visibility: hidden;">
                    <div class="signature-title">Menyetujui,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">________________</div>
                    <div class="signature-role">________________</div>
                </div>
            </div>
            
            <div class="footer-info">
                <p><strong>Dicetak oleh:</strong> {{ auth()->user() ? auth()->user()->name : 'System' }} ({{ auth()->user() ? ucfirst(auth()->user()->role) : 'Administrator' }})</p>
                <p>{{ now()->locale('id')->translatedFormat('d F Y, H:i:s') }}</p>
                <p>SMARTCLASS - Sistem Manajemen Kelas Digital</p>
            </div>
        </div>
    </div>
</body>
</html>

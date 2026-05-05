<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - {{ $monthName }}</title>
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
        .summary-income .summary-value { color: #000; }
        .summary-expense .summary-value { color: #000; }
        .summary-balance .summary-value { color: #000; }
        
        /* Table Styles */
        .table-container { margin: 25px 0; }
        table { width: 100%; border-collapse: collapse; border: 2px solid #000; font-size: 11px; }
        th { background: #f0f0f0; border: 1px solid #000; padding: 10px 8px; text-align: left; font-weight: 600; font-size: 11px; }
        td { border: 1px solid #000; padding: 8px; font-size: 11px; }
        .text-right { text-align: right; }
        .amount { font-weight: 600; font-family: 'Courier New', monospace; }
        .income { color: #000; }
        .expense { color: #000; }
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 600; }
        .status-income { background: #d4edda; color: #155724; }
        .status-expense { background: #f8d7da; color: #721c24; }
        
        /* Total Row */
        .total-row { background: #f0f0f0; font-weight: 600; }
        .total-row td { padding: 10px 8px; font-size: 12px; }
        
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
            th, td { padding: 6px 5px; font-size: 9px; line-height: 1.2; }
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
                    <div class="report-title">Laporan Keuangan Kelas</div>
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

        @if($transactions->count() > 0)
            <div class="summary">
                <div class="summary-card summary-income">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value">Rp {{ number_format($income, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card summary-expense">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value">Rp {{ number_format($expense, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card summary-balance">
                    <div class="summary-label">Saldo Akhir</div>
                    <div class="summary-value">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="12%">Tanggal</th>
                            <th width="35%">Keterangan</th>
                            <th width="20%">Siswa</th>
                            <th width="13%">Jenis</th>
                            <th width="20%" class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $t)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->date)->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                <td>{{ $t->description }}</td>
                                <td>{{ $t->student->name ?? '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $t->type == 'income' ? 'status-income' : 'status-expense' }}">
                                        {{ $t->type == 'income' ? 'MASUK' : 'KELUAR' }}
                                    </span>
                                </td>
                                <td class="text-right amount {{ $t->type == 'income' ? 'income' : 'expense' }}">
                                    {{ $t->type == 'income' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="4" class="text-right">TOTAL:</td>
                            <td class="text-right">
                                {{ $income >= $expense ? '+' : '-' }} Rp {{ number_format(abs($balance), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <div class="no-data-icon">📊</div>
                <h2>Belum ada transaksi</h2>
                <p>Transaksi keuangan untuk {{ $monthName }} {{ $year }} belum ada.</p>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Mengetahui, Kepala Sekolah SMARTCLASS</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </div>
</body>
</html>

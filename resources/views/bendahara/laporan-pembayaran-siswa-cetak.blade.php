<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran Siswa - {{ $monthName }} {{ $year }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: white; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #10b981; padding-bottom: 20px; }
        .logo { width: 80px; height: 80px; margin: 0 auto 10px; }
        h1 { color: #1f293b; font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .period { font-size: 18px; color: #64748b; font-weight: 500; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin: 30px 0; }
        .summary-card { background: #f0fdf4; padding: 20px; border-radius: 12px; text-align: center; border-left: 5px solid #10b981; }
        .summary-value { font-size: 28px; font-weight: 800; color: #166534; margin: 8px 0; }
        .students-section { margin: 40px 0; }
        .student-group { margin-bottom: 40px; page-break-inside: avoid; }
        .student-header { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 16px 20px; border-radius: 12px 12px 0 0; margin-bottom: 0; }
        .student-name { font-size: 20px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 0 0 12px 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: #f8fafc; color: #374151; padding: 16px 12px; text-align: left; font-weight: 600; font-size: 14px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 14px 12px; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background: #f8fafc; }
        .status-paid { color: #166534; font-weight: 600; }
        .status-unpaid { color: #dc2626; font-weight: 600; }
        .week-header { background: #ecfdf5; font-weight: 600; color: #065f46; }
        .total-row { background: #f0fdf4; font-weight: 700; font-size: 16px; border-top: 3px solid #10b981; }
        .footer { margin-top: 60px; text-align: center; padding-top: 30px; border-top: 2px dashed #d1d5db; color: #6b7280; font-size: 14px; }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .container { padding: 20px 10px; } }
        @page { margin: 1.5cm; size: A4; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMARTCLASS" class="logo" onerror="this.style.display='none'">
            <h1>LAPORAN PEMBAYARAN SISWA MINGGUAN</h1>
            <div class="period">{{ $monthName }} {{ $year }}</div>
            <div>Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</div>
            <div>Bendahara Kelas</div>
        </div>

        @if($payments->count() > 0)
            <div class="summary">
                <div class="summary-card">
                    <div>Total Tagihan</div>
                    <div class="summary-value">Rp {{ number_format($totalBills, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div>Sudah Dibayar</div>
                    <div class="summary-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div>Tunggakan</div>
                    <div class="summary-value text-red-600">Rp {{ number_format($totalBills - $totalPaid, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card">
                    <div>Jumlah Siswa</div>
                    <div class="summary-value">{{ $paymentsByStudent->count() }}</div>
                </div>
            </div>

            @foreach($paymentsByStudent as $studentId => $studentPayments)
                @php $student = $studentPayments->first()->student; @endphp
                <div class="students-section">
                    <div class="student-header">
                        <div class="student-name">{{ $student->name }}</div>
                        <div style="font-size: 14px; opacity: 0.9;">{{ count($studentPayments) }} tagihan | Rp {{ number_format($studentPayments->sum('amount'), 0, ',', '.') }} total</div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th width="30%">Minggu</th>
                                <th width="25%">Status</th>
                                <th width="25%">Tanggal Bayar</th>
                                <th width="20%" class="text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentPayments->sortBy('week_number') as $payment)
                                <tr>
                                    <td>Minggu {{ $payment->week_number }}</td>
                                    <td>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status == 'paid' ? 'bg-green-100 text-green-800 status-paid' : 'bg-red-100 text-red-800 status-unpaid' }}">
                                            {{ $payment->status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->locale('id')->isoFormat('D MMM YY') : '-' }}</td>
                                    <td class="text-right font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3" class="text-right pr-4">TOTAL:</td>
                                <td class="text-right">Rp {{ number_format($studentPayments->sum('amount'), 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 80px; color: #6b7280;">
                <div style="font-size: 5rem; margin-bottom: 30px;">📋</div>
                <h2 style="font-size: 28px; margin-bottom: 15px;">Belum ada data pembayaran</h2>
                <p>Data pembayaran siswa untuk {{ $monthName }} {{ $year }} belum tersedia.</p>
            </div>
        @endif

        <div class="footer">
            <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</p>
            <p>{{ now()->locale('id')->translatedFormat('d F Y, H:i:s') }}</p>
            <p style="font-size: 12px; margin-top: 20px;">SMARTCLASS - Sistem Manajemen Kelas Digital</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - <?php echo e($monthName); ?> <?php echo e($year); ?></title>
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
        .summary-income .summary-value { color: #000; }
        .summary-expense .summary-value { color: #000; }
        .summary-balance .summary-value { color: #000; }
        
        /* Tabel Transaksi */
        .table-container { margin: 25px 0; }
        table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
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
                <div class="school-contact">Laporan Keuangan Resmi</div>
            </div>
            <div class="school-logo">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo SMARTCLASS" class="logo" onerror="this.style.display='none'">
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="report-title">
            <h1>Laporan Keuangan Kelas</h1>
            <div class="report-period">Periode: <?php echo e($monthName); ?> <?php echo e($year); ?></div>
            <div class="report-info">Dicetak pada: <?php echo e(now()->locale('id')->translatedFormat('d F Y, H:i')); ?> | Bendahara Kelas</div>
        </div>

        <?php if($transactions->count() > 0): ?>
            <div class="summary">
                <div class="summary-card summary-income">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value">Rp <?php echo e(number_format($income, 0, ',', '.')); ?></div>
                </div>
                <div class="summary-card summary-expense">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value">Rp <?php echo e(number_format($expense, 0, ',', '.')); ?></div>
                </div>
                <div class="summary-card summary-balance">
                    <div class="summary-label">Saldo Akhir</div>
                    <div class="summary-value">Rp <?php echo e(number_format($balance, 0, ',', '.')); ?></div>
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
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($t->date)->locale('id')->isoFormat('D MMM YYYY')); ?></td>
                                <td><?php echo e($t->description); ?></td>
                                <td><?php echo e($t->student->name ?? '-'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo e($t->type == 'income' ? 'status-income' : 'status-expense'); ?>">
                                        <?php echo e($t->type == 'income' ? 'MASUK' : 'KELUAR'); ?>

                                    </span>
                                </td>
                                <td class="text-right amount <?php echo e($t->type == 'income' ? 'income' : 'expense'); ?>">
                                    <?php echo e($t->type == 'income' ? '+' : '-'); ?> Rp <?php echo e(number_format($t->amount, 0, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="total-row">
                            <td colspan="4" class="text-right">TOTAL:</td>
                            <td class="text-right">
                                <?php echo e($income >= $expense ? '+' : '-'); ?> Rp <?php echo e(number_format(abs($balance), 0, ',', '.')); ?>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon">📊</div>
                <h2>Belum ada transaksi</h2>
                <p>Transaksi keuangan untuk <?php echo e($monthName); ?> <?php echo e($year); ?> belum ada.</p>
            </div>
        <?php endif; ?>

        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">Mengetahui,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo e(auth()->user()->name); ?></div>
                    <div class="signature-role"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
                </div>
                <div class="signature-box" style="visibility: hidden;">
                    <div class="signature-title">Menyetujui,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">________________</div>
                    <div class="signature-role">________________</div>
                </div>
            </div>
            
            <div class="footer-info">
                <p><strong>Dicetak oleh:</strong> <?php echo e(auth()->user()->name); ?> (<?php echo e(ucfirst(auth()->user()->role)); ?>)</p>
                <p><?php echo e(now()->locale('id')->translatedFormat('d F Y, H:i:s')); ?></p>
                <p>SMARTCLASS - Sistem Manajemen Kelas Digital</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/laporan-keuangan-cetak.blade.php ENDPATH**/ ?>
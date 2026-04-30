<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - <?php echo e($monthName); ?> <?php echo e($year); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: white; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #3b82f6; padding-bottom: 20px; }
        .logo { width: 80px; height: 80px; margin: 0 auto 10px; }
        h1 { color: #1f293b; font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .period { font-size: 18px; color: #64748b; font-weight: 500; }
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
        .summary-card { background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; border-left: 5px solid; }
        .summary-income { border-left-color: #10b981; }
        .summary-expense { border-left-color: #ef4444; }
        .summary-balance { border-left-color: #3b82f6; }
        .summary-value { font-size: 32px; font-weight: 800; margin: 8px 0; }
        .summary-income .summary-value { color: #10b981; }
        .summary-expense .summary-value { color: #ef4444; }
        .summary-balance .summary-value { color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 18px 12px; text-align: left; font-weight: 600; font-size: 14px; }
        td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background: #f8fafc; }
        tr:last-child td { border-bottom: none; }
        .amount { font-weight: 600; font-family: 'Courier New', monospace; }
        .income { color: #10b981; }
        .expense { color: #ef4444; }
        .footer { margin-top: 50px; text-align: center; padding-top: 30px; border-top: 2px dashed #e5e7eb; color: #6b7280; font-size: 14px; }
        @media print { body { -webkit-print-color-adjust: exact; } .container { padding: 20px 10px; } }
        @page { margin: 1.5cm; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo SMARTCLASS" class="logo" onerror="this.style.display='none'">
            <h1>LAPORAN KEUANGAN KELAS</h1>
            <div class="period"><?php echo e($monthName); ?> <?php echo e($year); ?></div>
            <div>Dicetak pada: <?php echo e(now()->locale('id')->translatedFormat('d F Y, H:i')); ?></div>
            <div>Bendahara Kelas</div>
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

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Siswa</th>
                        <th>Jenis</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($t->date)->locale('id')->isoFormat('D MMM YYYY')); ?></td>
                            <td><?php echo e($t->description); ?></td>
                            <td><?php echo e($t->student->name ?? '-'); ?></td>
                            <td>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo e($t->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo e($t->type == 'income' ? 'MASUK' : 'KELUAR'); ?>

                                </span>
                            </td>
                            <td class="text-right amount <?php echo e($t->type == 'income' ? 'income' : 'expense'); ?>">
                                <?php echo e($t->type == 'income' ? '+' : '-'); ?> Rp <?php echo e(number_format($t->amount, 0, ',', '.')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr style="background: #f8fafc; font-weight: 600;">
                        <td colspan="4" class="text-right pr-4">TOTAL:</td>
                        <td class="text-right"></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; color: #6b7280;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📊</div>
                <h2 style="font-size: 24px; margin-bottom: 10px;">Belum ada transaksi</h2>
                <p>Transaksi keuangan untuk <?php echo e($monthName); ?> <?php echo e($year); ?> belum ada.</p>
            </div>
        <?php endif; ?>

        <div class="footer">
            <p><strong>Dicetak oleh:</strong> <?php echo e(auth()->user()->name); ?> (<?php echo e(ucfirst(auth()->user()->role)); ?>)</p>
            <p><?php echo e(now()->locale('id')->translatedFormat('d F Y, H:i:s')); ?></p>
            <p style="font-size: 12px; margin-top: 20px;">SMARTCLASS - Sistem Manajemen Kelas Digital</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/laporan-keuangan-cetak.blade.php ENDPATH**/ ?>
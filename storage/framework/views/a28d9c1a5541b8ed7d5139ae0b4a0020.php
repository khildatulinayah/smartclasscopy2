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
            line-height:1.5;
        }

        .container{ padding:25px; }

        .header{ width:100%; margin-bottom:25px; }
        .header-table{ width:100%; border-collapse:collapse; }
        .header-table td{ vertical-align:top; }
        .school-info{ text-align:center; }
        .school-name{ font-size:22px; font-weight:bold; text-transform:uppercase; }
        .school-address{ font-size:11px; margin-top:4px; }
        .report-title{ margin-top:15px; font-size:18px; font-weight:bold; text-transform:uppercase; }
        .period{ margin-top:5px; font-size:12px; }
        .line{ margin-top:18px; border-top:2px solid #000; border-bottom:1px solid #000; height:4px; }

        .summary{ margin:20px 0; }
        .summary-table{ width:100%; border-collapse:collapse; }
        .summary-table td{ border:1px solid #000; padding:12px; text-align:center; }
        .summary-title{ font-size:11px; margin-bottom:6px; font-weight:bold; }
        .summary-value{ font-size:16px; font-weight:bold; }

        .main-table{ width:100%; border-collapse:collapse; margin-top:10px; }
        .main-table th{
            background:#eaeaea;
            border:1px solid #000;
            padding:10px 8px;
            text-align:center;
            font-size:11px;
        }
        .main-table td{ border:1px solid #000; padding:8px; font-size:11px; }

        .text-center{ text-align:center; }
        .text-right{ text-align:right; }

        .total-row{ background:#f3f3f3; font-weight:bold; }

        .footer{ margin-top:60px; width:100%; }
        .signature{ width:250px; text-align:center; float:right; }
        .signature-space{ height:70px; }
        .signature-name{ font-weight:bold; text-decoration:underline; }

        @page{ margin:1.2cm; size:A4 landscape; }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <table class="header-table">
            <tr>
                <td width="70%" class="school-info">
                    <div class="school-name">SMARTCLASS</div>
                    <div class="school-address">Sistem Manajemen Keuangan dan Administrasi Kelas</div>
                    <div class="report-title">Laporan Keuangan Tahunan</div>
                    <div class="period">Periode Januari - <?php echo e($endMonthName); ?> Tahun <?php echo e($year); ?></div>
                </td>
                <td width="15%"></td>
            </tr>
        </table>
        <div class="line"></div>
    </div>

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td width="33%">
                    <div class="summary-title">TOTAL UANG MASUK</div>
                    <div class="summary-value">Rp <?php echo e(number_format($incomeTotal,0,',','.')); ?></div>
                </td>
                <td width="33%">
                    <div class="summary-title">TOTAL UANG KELUAR</div>
                    <div class="summary-value">Rp <?php echo e(number_format($expenseTotal,0,',','.')); ?></div>
                </td>
                <td width="34%">
                    <div class="summary-title">SALDO AKHIR</div>
                    <div class="summary-value">Rp <?php echo e(number_format($balanceTotal,0,',','.')); ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top:20px; font-weight:bold; text-transform:uppercase;">
        Rekap per Bulan (Uang Masuk & Uang Keluar)
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="20%">Bulan</th>
                <th width="30%">Uang Masuk</th>
                <th width="30%">Uang Keluar</th>
                <th width="20%">Saldo Bulan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $monthly; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($row['monthName']); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($row['income'],0,',','.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($row['expense'],0,',','.')); ?></td>
                    <td class="text-right">Rp <?php echo e(number_format($row['cum_balance'],0,',','.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <tr class="total-row">
                <td class="text-center">TOTAL</td>
                <td class="text-right">Rp <?php echo e(number_format($incomeTotal,0,',','.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($expenseTotal,0,',','.')); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($balanceTotal,0,',','.')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <div><?php echo e(now()->translatedFormat('d F Y')); ?></div>
            <div style="margin-top:5px;">Kepala Sekolah</div>
            <div class="signature-space"></div>
            <div class="signature-name"><?php echo e(auth()->user()->name); ?></div>
        </div>
    </div>

</div>
</body>
</html>

<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/laporan-keuangan-tahunan-perbulan-cetak.blade.php ENDPATH**/ ?>
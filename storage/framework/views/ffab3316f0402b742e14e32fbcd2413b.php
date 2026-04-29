<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran Siswa <?php echo e($monthName); ?></title>
    <style>
        @page {
            margin: 0.25in;
            size: A4;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0.25in 0.3in;
        }
        h1 {
            font-size: 24pt;
            text-align: center;
            margin-bottom: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 3px solid #000;
            padding-bottom: 5pt;
        }
        h2 {
            font-size: 18pt;
            text-align: center;
            margin: 20pt 0 10pt 0;
            font-weight: bold;
        }
        p.subtitle {
            font-size: 14pt;
            text-align: center;
            margin-bottom: 20pt;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20pt;
            border: 2px solid #000;
        }
        th, td {
            border: 1px solid #000;
            padding: 6pt 4pt;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 11pt;
        }
        td.name {
            text-align: left;
            font-weight: bold;
            font-size: 11pt;
        }
        .footer {
            margin-top: 40pt;
            text-align: center;
            font-size: 12pt;
        }
        .total-row {
            font-weight: bold;
            font-size: 12pt;
            background-color: #e9ecef;
        }
        .status-lunas {
            background-color: #d4edda !important;
            color: #155724 !important;
        }
        .status-belum {
            background-color: #fff3cd !important;
            color: #856404 !important;
        }
        .status-tunggak {
            background-color: #f8d7da !important;
            color: #721c24 !important;
        }
    </style>
</head>
<body>
    <h1>CETAK LAPORAN PEMBAYARAN SISWA (MINGGUAN)</h1>
    <h2><?php echo e($monthName); ?></h2>
    <p class="subtitle">Berisi data pembayaran kas siswa per minggu dalam 1 bulan (Mg1-Mg4)</p>

    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Nama Siswa</th>
                <th style="width: 9%;">Mg 1</th>
                <th style="width: 9%;">Mg 2</th>
                <th style="width: 9%;">Mg 3</th>
                <th style="width: 9%;">Mg 4</th>
                <th style="width: 11%;">Total Bayar</th>
                <th style="width: 11%;">Tunggakan</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalPaid = 0; $totalArrears = 0; $lunasCount = 0; ?>
            <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $studentPayments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $student = $studentPayments->first()->student;
                    $paidCount = $studentPayments->where('status', 'paid')->count();
                    $paidAmount = $paidCount * 5000;
                    $arrears = 20000 - $paidAmount;
                    $status = $paidCount == 4 ? 'LUNAS' : ($paidCount == 0 ? 'BELUM BAYAR' : 'TUNGGAKAN');
                    $statusClass = $paidCount == 4 ? 'status-lunas' : ($paidCount == 0 ? 'status-belum' : 'status-tunggak');
                    $totalPaid += $paidAmount;
                    $totalArrears += $arrears;
                    if ($paidCount == 4) $lunasCount++;
                ?>
                <tr>
                    <td class="name"><?php echo e($student->name); ?></td>
                    <?php for($w = 1; $w <= 4; $w++): ?>
                        <?php $payment = $studentPayments->where('week_number', $w)->first(); ?>
                        <td style="font-size: 14pt; font-weight: bold;"><?php echo e($payment && $payment->status == 'paid' ? '✓' : '✗'); ?></td>
                    <?php endfor; ?>
                    <td>Rp <?php echo e(number_format($paidAmount, 0, ',', '.')); ?></td>
                    <td>Rp <?php echo e(number_format($arrears, 0, ',', '.')); ?></td>
                    <td class="<?php echo e($statusClass); ?> font-weight: bold;"><?php echo e($status); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="total-row">
                <td colspan="5"><strong>TOTAL</strong></td>
                <td><strong>Rp <?php echo e(number_format($totalPaid, 0, ',', '.')); ?></strong></td>
                <td><strong>Rp <?php echo e(number_format($totalArrears, 0, ',', '.')); ?></strong></td>
                <td><strong><?php echo e($lunasCount); ?> LUNAS</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Bendahara Kelas</strong></p>
        <p><?php echo e(now()->locale('id')->isoFormat('D MMMM Y')); ?></p>
        <p>Halaman 1 dari 1</p>
    </div>
</body>
</html>

<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/laporan-pembayaran-siswa-cetak.blade.php ENDPATH**/ ?>
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
                        <?php echo e(\Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F Y')); ?>

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
                        Rp <?php echo e(number_format($income,0,',','.')); ?>

                    </div>
                </td>

                <td width="33%">
                    <div class="summary-title">
                        TOTAL PENGELUARAN
                    </div>

                    <div class="summary-value">
                        Rp <?php echo e(number_format($expense,0,',','.')); ?>

                    </div>
                </td>

                <td width="34%">
                    <div class="summary-title">
                        SALDO AKHIR
                    </div>

                    <div class="summary-value">
                        Rp <?php echo e(number_format($balance,0,',','.')); ?>

                    </div>
                </td>

            </tr>
        </table>

    </div>

    <!-- TABLE -->
    <table class="main-table">

        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="20%">Siswa</th>
                <th width="10%">Jenis</th>
                <th width="15%">Nominal</th>
            </tr>
        </thead>

        <tbody>

        <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            <tr>

                <td class="text-center">
                    <?php echo e($index + 1); ?>

                </td>

                <td>
                    <?php echo e(\Carbon\Carbon::parse($t->date)->translatedFormat('d F Y')); ?>

                </td>

                <td>
                    <?php echo e($t->description); ?>

                </td>

                <td>
                    <?php echo e($t->student->name ?? '-'); ?>

                </td>

                <td class="text-center">
                    <?php echo e($t->type == 'income' ? 'Masuk' : 'Keluar'); ?>

                </td>

                <td class="text-right <?php echo e($t->type == 'income' ? 'income' : 'expense'); ?>">
                    <?php echo e($t->type == 'income' ? '+' : '-'); ?>

                    Rp <?php echo e(number_format($t->amount,0,',','.')); ?>

                </td>

            </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

            <tr>
                <td colspan="6" class="text-center">
                    Tidak ada transaksi.
                </td>
            </tr>

        <?php endif; ?>

            <tr class="total-row">

                <td colspan="5" class="text-right">
                    SALDO AKHIR
                </td>

                <td class="text-right">
                    Rp <?php echo e(number_format($balance,0,',','.')); ?>

                </td>

            </tr>

        </tbody>

    </table>

    <!-- FOOTER -->
    <div class="footer">

        <div class="signature">

            <div>
                <?php echo e(now()->translatedFormat('d F Y')); ?>

            </div>

            <div style="margin-top:5px;">
                Kepala Sekolah
            </div>

            <div class="signature-space"></div>

            <div class="signature-name">
                <?php echo e(auth()->user()->name); ?>

            </div>

        </div>

    </div>

</div>

</body>
</html><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/laporan-keuangan-cetak.blade.php ENDPATH**/ ?>
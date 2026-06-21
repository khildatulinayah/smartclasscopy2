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



    <!-- TABLE PENGANTAR: Kas Siswa (dirangkum per minggu) -->
    <div style="margin-top:20px; font-weight:bold; text-transform:uppercase;">A. Tabel Pemasukan</div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="20%">Jenis</th>
                <th width="25%">Nominal</th>
            </tr>
        </thead>
        <tbody>

        <?php
            $incomeRows = collect($incomeRows ?? []);
            $transactionsForIncome = isset($transactions)
                ? collect($transactions)
                    ->where('type', 'income')
                    ->sortBy('date')
                : collect();

            // Gabungkan pemasukan weekly (dibuat dari weekly_payments) dengan pemasukan manual (yang tidak punya weekly_payment_id)
            // weekly_payment_id: ambil dari transaksi yang terhubung ke weekly_payments
            $manualIncomeRows = $transactionsForIncome
                ->filter(fn ($tx) => empty($tx->weekly_payment_id))
                ->map(function ($tx) {
                    return [
                        'label' => \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y'),
                        'week' => null,
                        'student_count' => null,
                        'per_student_amount' => null,
                        'description' => $tx->description ?? 'PEMASUKAN',
                        'amount' => (float) ($tx->amount ?? 0),
                        'is_manual' => true,
                    ];
                })
                ->values();

            $mergedIncomeRows = $incomeRows
                ->map(function ($r) {
                    return array_merge(['is_manual' => false], $r);
                })
                ->concat($manualIncomeRows)
                ->sortBy(function ($r) {
                    return $r['label'] ?? '';
                })
                ->values();
        ?>

        <?php if($mergedIncomeRows->count() > 0): ?>
            <?php $__currentLoopData = $mergedIncomeRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($row['label']); ?></td>
                    <td>
                        <?php
                            $weekNum = $row['week'] ?? null;
                            $studentCount = (int)($row['student_count'] ?? 0);
                            $perStudentAmount = (float)($row['per_student_amount'] ?? 0);

                            $ket = $row['is_manual'] ?? false
                                ? ($row['description'] ?? 'PEMASUKAN')
                                : 'Kas siswa';

                            if (!($row['is_manual'] ?? false) && !empty($weekNum)) {
                                $ket .= ' - Minggu ke-' . $weekNum;
                            }

                            // format: nominal kasnya berapa x jumlah siswa (khusus kas siswa weekly)
                            if (!($row['is_manual'] ?? false) && $studentCount > 0) {
                                $ket .= ' (Rp ' . number_format($perStudentAmount, 0, ',', '.') . ' x ' . $studentCount . ' siswa)';
                            }
                        ?>
                        <?php echo e($ket); ?>

                    </td>
                    <td class="text-center">Masuk</td>
                    <td class="text-right income">
                        + Rp <?php echo e(number_format($row['amount'] ?? 0, 0, ',', '.')); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Tidak ada pemasukan.</td>
            </tr>
        <?php endif; ?>





        </tbody>
    </table>

    <div style="margin-top:20px; font-weight:bold; text-transform:uppercase;">B. Tabel Pengeluaran</div>

        <!-- TABLE PENGELUARAN -->

        <!-- TABLE PENGELUARAN -->
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Keterangan</th>
                <th width="20%">Jenis</th>
                <th width="25%">Nominal</th>
            </tr>
        </thead>

        <tbody>

        <?php
            $expenseRows = collect($expenseRows ?? []);
        ?>

        <?php
            if ($expenseRows->count() === 0 && isset($transactions)) {
                $expenseRows = collect($transactions)
                    ->where('type', 'expense')
                    ->sortBy('date')
                    ->map(function ($tx) {
                        // Untuk pengeluaran yang terhubung ke weekly payment (jarang terjadi,
                        // mis. refund/pengembalian), tanggal tetap ditampilkan apa adanya
                        // karena kolom 'date' transaksi tetap relevan untuk baris pengeluaran;
                        // periode laporan (bulan/tahun) sudah ditentukan oleh weekly_payment_id
                        // di query controller, bukan oleh tanggal ini.
                        return [
                            'label' => \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y'),
                            'amount' => (float)($tx->amount ?? 0),
                            'description' => $tx->description ?? 'PENGELUARAN',
                            'receipt_path' => $tx->receipt_path ?? null,
                        ];
                    })
                    ->values();
            }
        ?>

        <?php if($expenseRows->count() > 0): ?>
            <?php $__currentLoopData = $expenseRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($row['label']); ?></td>
                    <td><?php echo e($row['description'] ?? 'PENGELUARAN'); ?></td>
                    <td class="text-center">Keluar</td>
                    <td class="text-right expense">
                        - Rp <?php echo e(number_format($row['amount'] ?? 0, 0, ',', '.')); ?>

                        <?php if(!empty($row['receipt_path'])): ?>
                            <div style="margin-top:6px;">
                                <img src="<?php echo e(asset('public/' . $row['receipt_path'])); ?>" alt="Bukti" style="max-height:60px; max-width:160px; object-fit:contain;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Tidak ada pengeluaran.</td>
            </tr>
        <?php endif; ?>


        <tr class="total-row">
            <td colspan="3" class="text-right">SALDO AKHIR</td>
            <td colspan="2" class="text-right">Rp <?php echo e(number_format($balance,0,',','.')); ?></td>
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
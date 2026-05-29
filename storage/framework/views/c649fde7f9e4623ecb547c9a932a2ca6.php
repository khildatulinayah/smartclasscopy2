<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - <?php echo e($monthName); ?></title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: DejaVu Sans, sans-serif;
            color:#222;
            font-size:11px;
            line-height:1.4;
        }

        .container{
            padding:20px;
        }

        /* HEADER */

        .header{
            margin-bottom:20px;
        }

        .header-table{
            width:100%;
            border-collapse:collapse;
        }

        .header-table td{
            vertical-align:top;
        }

        .logo{
            width:85px;
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

        /* HOLIDAY */

        .holiday-box{
            margin-top:15px;
            margin-bottom:15px;
            border:1px solid #000;
            padding:10px;
            background:#f7f7f7;
            font-size:10px;
        }

        .holiday-title{
            font-weight:bold;
            margin-bottom:6px;
        }

        .holiday-item{
            margin-bottom:3px;
        }

        /* TABLE */

        .attendance-table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        .attendance-table th{
            background:#ececec;
            border:1px solid #000;
            padding:5px 3px;
            text-align:center;
            font-size:10px;
        }

        .attendance-table td{
            border:1px solid #000;
            padding:4px 2px;
            text-align:center;
            font-size:9px;
        }

        .student-name{
            text-align:left !important;
            padding-left:6px !important;
            font-size:10px;
        }

        .day-col{
            width:20px;
        }

        .weekend{
            background:#efefef;
            color:#888;
        }

        .status-hadir{
            background:#d4edda;
            font-weight:bold;
        }

        .status-sakit{
            background:#fff3cd;
            font-weight:bold;
        }

        .status-izin{
            background:#d1ecf1;
            font-weight:bold;
        }

        .status-alpha{
            background:#f8d7da;
            font-weight:bold;
        }

        .status-libur{
            background:#e2e3e5;
            font-weight:bold;
        }

        .status-belum{
            background:#ffffff;
            color:#999;
        }

        .total-row{
            background:#f1f1f1;
            font-weight:bold;
        }

        /* LEGEND */

        .legend{
            margin-top:20px;
            border:1px solid #000;
            padding:10px;
            background:#fafafa;
        }

        .legend-title{
            font-weight:bold;
            margin-bottom:8px;
        }

        .legend-table{
            width:100%;
        }

        .legend-table td{
            padding:3px 0;
            font-size:10px;
        }

        /* FOOTER */

        .footer{
            margin-top:50px;
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

        /* NO DATA */

        .no-data{
            text-align:center;
            margin-top:60px;
            font-size:13px;
        }

        @page{
            margin:1cm;
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

                <td width="15%">
                    <img
                        src="<?php echo e(public_path('images/logo2.png')); ?>"
                        class="logo">
                </td>

                <td width="70%" class="school-info">

                    <div class="school-name">
                        SMARTCLASS
                    </div>

                    <div class="school-address">
                        Sistem Administrasi dan Absensi Siswa
                    </div>

                    <div class="report-title">
                        Laporan Absensi Siswa
                    </div>

                    <div class="period">
                        <?php echo e(\Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F Y')); ?>

                    </div>

                </td>

                <td width="15%"></td>

            </tr>

        </table>

        <div class="line"></div>

    </div>

    <!-- HOLIDAY -->

    <?php if($holidays->count() > 0): ?>

    <div class="holiday-box">

        <div class="holiday-title">
            Informasi Hari Libur
        </div>

        <?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="holiday-item">
                •
                <?php echo e(\Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y')); ?>

                :
                <?php echo e($note); ?>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

    <?php endif; ?>

    <!-- TABLE -->

    <?php if($students->count() > 0): ?>

    <table class="attendance-table">

        <thead>

        <tr>

            <th rowspan="2">
                No
            </th>

            <th rowspan="2">
                Nama Siswa
            </th>

            <th colspan="<?php echo e($daysInMonth); ?>">
                Kehadiran
            </th>

            <th colspan="3">
                Total
            </th>

        </tr>

        <tr>

            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>

                <?php
                    $date = \Carbon\Carbon::create($year, $month, $day);

                    $isWeekend =
                        in_array($date->dayOfWeek, [0,6]);
                ?>

                <th class="day-col <?php echo e($isWeekend ? 'weekend' : ''); ?>">
                    <?php echo e($day); ?>

                </th>

            <?php endfor; ?>

            <th>S</th>
            <th>I</th>
            <th>A</th>

        </tr>

        </thead>

        <tbody>

        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php

                $studentAttendances =
                    $attendancesByStudent->get(
                        $student->id,
                        collect()
                    );

                $attendanceMap = [];

                foreach($studentAttendances as $att){
                    $attendanceMap[
                        $att->date->format('Y-m-d')
                    ] = $att;
                }

                $sakit = 0;
                $izin = 0;
                $alpha = 0;

            ?>

            <tr>

                <td>
                    <?php echo e($index + 1); ?>

                </td>

                <td class="student-name">
                    <?php echo e($student->name); ?>

                </td>

                <?php for($day = 1; $day <= $daysInMonth; $day++): ?>

                    <?php

                        $date =
                            \Carbon\Carbon::create(
                                $year,
                                $month,
                                $day
                            );

                        $dateString =
                            $date->format('Y-m-d');

                        $isWeekend =
                            in_array(
                                $date->dayOfWeek,
                                [0,6]
                            );

                        $isHoliday =
                            $holidays->has($dateString);

                        $attendance =
                            $attendanceMap[$dateString]
                            ?? null;

                        $status = '';
                        $class = '';

                        if($isWeekend){

                            $status = '-';
                            $class = 'weekend';

                        }elseif($isHoliday){

                            $status = 'L';
                            $class = 'status-libur';

                        }elseif($attendance){

                            switch($attendance->status){

                                case 'hadir':
                                    $status = 'H';
                                    $class = 'status-hadir';
                                    break;

                                case 'sakit':
                                    $status = 'S';
                                    $class = 'status-sakit';
                                    $sakit++;
                                    break;

                                case 'izin':
                                    $status = 'I';
                                    $class = 'status-izin';
                                    $izin++;
                                    break;

                                case 'alpha':
                                    $status = 'A';
                                    $class = 'status-alpha';
                                    $alpha++;
                                    break;

                                default:
                                    $status = '';
                                    $class = 'status-belum';
                            }

                        }else{

                            $status = '';
                            $class = 'status-belum';

                        }

                    ?>

                    <td class="day-col <?php echo e($class); ?>">
                        <?php echo e($status); ?>

                    </td>

                <?php endfor; ?>

                <td class="total-row">
                    <?php echo e($sakit); ?>

                </td>

                <td class="total-row">
                    <?php echo e($izin); ?>

                </td>

                <td class="total-row">
                    <?php echo e($alpha); ?>

                </td>

            </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- TOTAL -->

        <tr class="total-row">

            <td colspan="2">
                GRAND TOTAL
            </td>

            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>

                <td></td>

            <?php endfor; ?>

            <td>
                <?php echo e($stats['totalSakit']); ?>

            </td>

            <td>
                <?php echo e($stats['totalIzin']); ?>

            </td>

            <td>
                <?php echo e($stats['totalAlpha']); ?>

            </td>

        </tr>

        </tbody>

    </table>

    <?php else: ?>

    <div class="no-data">
        Belum ada data absensi siswa.
    </div>

    <?php endif; ?>

    <!-- LEGEND -->

    <div class="legend">

        <div class="legend-title">
            Keterangan
        </div>

        <table class="legend-table">

            <tr>
                <td><strong>H</strong> = Hadir</td>
                <td><strong>S</strong> = Sakit</td>
                <td><strong>I</strong> = Izin</td>
            </tr>

            <tr>
                <td><strong>A</strong> = Alpha</td>
                <td><strong>L</strong> = Hari Libur</td>
                <td><strong>-</strong> = Weekend</td>
            </tr>

        </table>

    </div>

    <!-- FOOTER -->

    <div class="footer">

        <div class="signature">

            <div>
                <?php echo e(now()->locale('id')->translatedFormat('d F Y')); ?>

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
</html><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/sekretaris/laporan-absensi-cetak.blade.php ENDPATH**/ ?>
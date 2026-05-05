<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - <?php echo e($monthName); ?></title>
    <center>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.4; color: #333; background: white; }
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
        .logo-right { width: 120px; height: 120px; }
        
        /* Table Styles */
        .attendance-table { width: 100%; border-collapse: collapse; margin-top: 15px; border: 2px solid #000; font-size: 11px; }
        .attendance-table th, .attendance-table td { border: 1px solid #000; padding: 5px 3px; text-align: center; font-size: 11px; vertical-align: middle; line-height: 1.3; }
        .attendance-table th { background: #f0f0f0; font-weight: bold; }
        
        /* Specific column widths */
        .attendance-table th:nth-child(1), .attendance-table td:nth-child(1) { width: 35px; font-size: 10px; } /* No */
        .attendance-table th:nth-child(2), .attendance-table td:nth-child(2) { width: 200px; text-align: left; font-size: 10px; } /* Siswa */
        /* All day columns should have equal width */
        .attendance-table th.day-col, .attendance-table td.day-col { width: 25px; min-width: 25px; max-width: 25px; font-size: 9px; padding: 3px 2px; }
        .attendance-table th:nth-last-child(3), .attendance-table td:nth-last-child(3) { width: 30px; font-size: 10px; } /* Total S */
        .attendance-table th:nth-last-child(2), .attendance-table td:nth-last-child(2) { width: 30px; font-size: 10px; } /* Total I */
        .attendance-table th:nth-last-child(1), .attendance-table td:nth-last-child(1) { width: 30px; font-size: 10px; } /* Total A */
        
        /* Status colors */
        .status-hadir { background: #d4edda; color: #155724; font-weight: bold; }
        .status-sakit { background: #fff3cd; color: #856404; font-weight: bold; }
        .status-izin { background: #d1ecf1; color: #0c5460; font-weight: bold; }
        .status-alpha { background: #f8d7da; color: #721c24; font-weight: bold; }
        .status-libur { background: #e2e3e5; color: #383d41; font-weight: bold; }
        .status-belum { background: #fff; color: #6c757d; font-weight: bold; }
        .weekend { background: #f8f9fa; color: #6c757d; }
        
        /* Total section styling */
        .total-section { background: #f0f0f0; font-weight: bold; }
        
        /* Signature section */
        .signature-section { margin-top: 50px; text-align: center; }
        .signature-box { display: inline-block; width: 300px; margin: 0 auto; }
        .signature-title { font-weight: bold; margin-bottom: 30px; }
        .signature-line { border-bottom: 1px solid #000; height: 40px; margin-bottom: 5px; }
        .signature-name { font-weight: bold; }
        
        /* Holiday information */
        .holiday-info { margin: 20px 0; padding: 8px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; font-size: 9px; }
        .holiday-info h4 { margin-bottom: 3px; color: #856404; font-size: 10px; }
        .holiday-info div { margin-bottom: 2px; }
        
        @media print { 
            body { -webkit-print-color-adjust: exact; font-size: 9px; } 
            .container { padding: 15px 10px; }
            .attendance-table { font-size: 9px; }
            .attendance-table th, .attendance-table td { padding: 3px 2px; font-size: 9px; line-height: 1.2; }
            .attendance-table th.day-col, .attendance-table td.day-col { width: 20px; min-width: 20px; max-width: 20px; font-size: 7px; padding: 2px 1px; }
            .attendance-table th:nth-child(2), .attendance-table td:nth-child(2) { width: 170px; font-size: 9px; }
            .school-name { font-size: 18px; }
            .report-title { font-size: 16px; }
            .logo-right { width: 100px; height: 100px; }
            .holiday-info { font-size: 8px; padding: 6px; }
            .holiday-info h4 { font-size: 9px; }
            .period-info { font-size: 9px; }
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
                    <div class="report-title">Daftar Kehadiran Siswa</div>
                    <div class="period-info">
                        <div class="period-item">
                            <span class="period-label">Bulan:</span>
                            <span><?php echo e(\Carbon\Carbon::create($year, $month)->locale('id')->format('F')); ?></span>
                        </div>
                        <div class="period-item">
                            <span class="period-label">Tahun:</span>
                            <span><?php echo e($year); ?></span>
                        </div>
                    </div>
                </div>
                <div>
                    <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="Logo" class="logo-right" onerror="this.style.display='none'">
                </div>
            </div>
        </div>

        <!-- Holiday Information -->
        <?php if($holidays->count() > 0): ?>
        <div class="holiday-info">
            <h4>Informasi Hari Libur:</h4>
            <?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>• <?php echo e(\Carbon\Carbon::parse($date)->locale('id')->format('d F Y')); ?>: <?php echo e($note); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <!-- Attendance Table -->
        <?php if($students->count() > 0): ?>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Siswa</th>
                        <th colspan="<?php echo e($daysInMonth ?? \Carbon\Carbon::create($year, $month)->daysInMonth); ?>">Kehadiran</th>
                        <th colspan="3">Total</th>
                    </tr>
                    <tr>
                        <?php
                            $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $date = \Carbon\Carbon::create($year, $month, $day);
                                $dateString = $date->format('Y-m-d');
                                $dayOfWeek = $date->dayOfWeek;
                                $isWeekend = in_array($dayOfWeek, [0, 6]); // Sunday=0, Saturday=6
                                $isHoliday = $holidays->has($dateString);
                                
                                echo '<th class="day-col ' . ($isWeekend ? 'weekend' : '') . ($isHoliday ? ' holiday' : '') . '">' . $day . '</th>';
                            }
                        ?>
                        <th>S</th>
                        <th>I</th>
                        <th>A</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $studentAttendances = $attendancesByStudent->get($student->id, collect());
                            $attendanceByDate = [];
                            
                            foreach ($studentAttendances as $att) {
                                $attendanceByDate[$att->date->format('Y-m-d')] = $att;
                            }
                            
                            $studentStats = [
                                'hadir' => 0,
                                'sakit' => 0,
                                'izin' => 0,
                                'alpha' => 0,
                                'belum' => 0
                            ];
                        ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($student->name); ?></td>
                            
                            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                                <?php
                                    $date = \Carbon\Carbon::create($year, $month, $day);
                                    $dateString = $date->format('Y-m-d');
                                    $dayOfWeek = $date->dayOfWeek;
                                    $isWeekend = in_array($dayOfWeek, [0, 6]);
                                    $isHoliday = $holidays->has($dateString);
                                    
                                    $status = '';
                                    $statusClass = '';
                                    
                                    if ($isWeekend) {
                                        $status = '-';
                                        $statusClass = 'weekend';
                                    } elseif ($isHoliday) {
                                        $status = 'L';
                                        $statusClass = 'status-libur';
                                    } else {
                                        $attendance = $attendanceByDate[$dateString] ?? null;
                                        
                                        if ($attendance) {
                                            switch ($attendance->status) {
                                                case 'hadir':
                                                    $status = 'H';
                                                    $statusClass = 'status-hadir';
                                                    $studentStats['hadir']++;
                                                    break;
                                                case 'sakit':
                                                    $status = 'S';
                                                    $statusClass = 'status-sakit';
                                                    $studentStats['sakit']++;
                                                    break;
                                                case 'izin':
                                                    $status = 'I';
                                                    $statusClass = 'status-izin';
                                                    $studentStats['izin']++;
                                                    break;
                                                case 'alpha':
                                                    $status = 'A';
                                                    $statusClass = 'status-alpha';
                                                    $studentStats['alpha']++;
                                                    break;
                                                default:
                                                    $status = '';
                                                    $statusClass = 'status-belum';
                                                    $studentStats['belum']++;
                                            }
                                        } else {
                                            $status = '';
                                            $statusClass = 'status-belum';
                                            $studentStats['belum']++;
                                        }
                                    }
                                ?>
                                <td class="day-col <?php echo e($statusClass); ?>"><?php echo e($status); ?></td>
                            <?php endfor; ?>
                            
                            <td class="total-section"><?php echo e($studentStats['sakit']); ?></td>
                            <td class="total-section"><?php echo e($studentStats['izin']); ?></td>
                            <td class="total-section"><?php echo e($studentStats['alpha']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <!-- Grand Total Row -->
                    <tr class="total-section">
                        <td colspan="2">GRAND TOTAL</td>
                        <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                            <?php
                                $date = \Carbon\Carbon::create($year, $month, $day);
                                $dateString = $date->format('Y-m-d');
                                $dayOfWeek = $date->dayOfWeek;
                                $isWeekend = in_array($dayOfWeek, [0, 6]);
                                $isHoliday = $holidays->has($dateString);
                                
                                if (!$isWeekend && !$isHoliday) {
                                    $dayTotal = 0;
                                    foreach ($students as $student) {
                                        $studentAttendances = $attendancesByStudent->get($student->id, collect());
                                        foreach ($studentAttendances as $att) {
                                            if ($att->date->format('Y-m-d') === $dateString && in_array($att->status, ['hadir', 'sakit', 'izin', 'alpha'])) {
                                                $dayTotal++;
                                                break;
                                            }
                                        }
                                    }
                                    echo '<td class="day-col">' . $dayTotal . '</td>';
                                } else {
                                    echo '<td class="day-col"></td>';
                                }
                            ?>
                        <?php endfor; ?>
                        <td><?php echo e($stats['totalSakit']); ?></td>
                        <td><?php echo e($stats['totalIzin']); ?></td>
                        <td><?php echo e($stats['totalAlpha']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; color: #6b7280;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📊</div>
                <h2 style="font-size: 24px; margin-bottom: 10px;">Belum ada data siswa</h2>
                <p>Data siswa untuk laporan ini belum tersedia.</p>
            </div>
        <?php endif; ?>

        <!-- Legend Section -->
        <div style="margin-top: 30px; padding: 15px; border: 1px solid #000; background: #f9f9f9;">
            <h4 style="margin-bottom: 10px; font-weight: bold;">Keterangan:</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px; font-size: 11px;">
                <div><strong>S</strong> = Sakit</div>
                <div><strong>I</strong> = Izin</div>
                <div><strong>A</strong> = Alpa</div>
                <div><strong>L</strong> = Libur</div>
                <div><strong>-</strong> = Weekend (Sabtu/Minggu)</div>
                <div><strong>Kosong</strong> = Belum Absen</div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Mengetahui, Kepala Sekolah SMARTCLASS</div>
                <div class="signature-line"></div>
                <div class="signature-name"><?php echo e(auth()->user()->name); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/sekretaris/laporan-absensi-cetak.blade.php ENDPATH**/ ?>
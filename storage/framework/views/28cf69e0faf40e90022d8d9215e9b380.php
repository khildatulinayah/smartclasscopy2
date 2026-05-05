<?php use Carbon\Carbon; ?>

<?php $__env->startSection('title', 'Pembayaran Mingguan'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
    <?php echo $__env->make('components.bendahara-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Pembayaran Mingguan</h1>
                    <p class="greeting-subtitle">Kelola dan pantau pembayaran kas siswa per minggu</p>
                </div>
            </section>

            <!-- Month Navigation -->
            <div class="flex items-center justify-center mb-8 space-x-4">
                <a href="?month=<?php echo e($prevMonth); ?>&year=<?php echo e($prevYear); ?>" class="nav-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Bulan Sebelumnya
                </a>
                <div class="bg-blue-500 text-white px-8 py-4 rounded-xl shadow-lg">
                    <div class="text-xl font-bold"><?php echo e($currentMonthName); ?></div>
                </div>
                <a href="?month=<?php echo e($nextMonth); ?>&year=<?php echo e($nextYear); ?>" class="nav-btn">
                    Bulan Selanjutnya
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
    
    <!-- Info Panel -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-blue-800">Data Pembayaran: <?php echo e($currentMonthName); ?></h3>
                <p class="text-sm text-blue-600">
                    <?php echo e($weeksInMonth); ?> minggu pembayaran • 
                    <?php echo e($totalBills); ?> tagihan untuk <?php echo e($totalStudents); ?> siswa
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-blue-600">Nominal per Minggu:</div>
                <div class="text-xl font-bold text-blue-800">Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?></div>
            </div>
        </div>
        <?php if(isset($wednesdayDates) && count($wednesdayDates) > 0): ?>
            <div class="mt-3 pt-3 border-t border-blue-200">
                <div class="text-sm text-blue-600">Jadwal pembayaran:</div>
                <div class="flex flex-wrap gap-2 mt-1">
                    <?php $__currentLoopData = $wednesdayDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                            Minggu <?php echo e($index + 1); ?>: <?php echo e($date->locale('id')->translatedFormat('d M')); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if(!empty($kasSettingWarning)): ?>
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
            <h3 class="text-sm font-semibold text-orange-800">Nominal Belum Diatur</h3>
            <p class="text-sm text-orange-700 mt-1"><?php echo e($kasSettingWarning); ?></p>
            <a href="<?php echo e(route('bendahara.kas.settings', ['month' => $month, 'year' => $year])); ?>" class="inline-block mt-3 text-sm font-semibold text-orange-700 hover:text-orange-900">
                Buka Pengaturan Kas
            </a>
        </div>
    <?php endif; ?>

    
    <?php if(isset($isCurrentMonth) && $isCurrentMonth): ?>
        <?php if(isset($isWednesday) && $isWednesday): ?>
            <div class="bg-red-500 text-white p-6 mb-6 rounded-xl text-center border-4 border-red-600 shadow-2xl">
                <h2 class="text-2xl font-bold mb-2 animate-pulse">🚨 HARI RABU - PEMBAYARAN KAS!</h2>
                <p class="text-lg">Prioritaskan <strong><?php echo e($currentWeekUnpaid); ?></strong> siswa untuk Minggu ke-<?php echo e($currentWeek); ?></p>
            </div>
        <?php else: ?>
            <div class="bg-yellow-400 text-black p-6 mb-6 rounded-xl text-center border-4 border-yellow-500 shadow-xl">
                <h2 class="text-xl font-bold mb-2">⏳ Selanjutnya: Hari Rabu</h2>
                <p class="text-lg">Rabu, <?php echo e($nextWednesday ?? 'Minggu ini'); ?> | <?php echo e($currentWeekUnpaid ?? 0); ?> belum bayar minggu ini</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Info Panel untuk Petunjuk Pembayaran -->
    <?php if($totalBills > 0 && $paidBills === 0): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800">⚠️ Belum Ada Transaksi Kas</h3>
                    <p class="text-sm text-yellow-700">
                        Anda perlu membuat transaksi pemasukan sebelum dapat mencatat pembayaran mingguan.
                    </p>
                </div>
                <div class="text-right">
                    <a href="<?php echo e(route('bendahara.kas')); ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-medium">
                        Buat Transaksi Kas
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo e($totalStudents); ?></div>
                    <div class="text-sm text-gray-500">Total Siswa</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo e($totalBills); ?></div>
                    <div class="text-sm text-gray-500">Total Tagihan</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600"><?php echo e($paidBills); ?></div>
                    <div class="text-sm text-gray-500">Sudah Bayar</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-red-600"><?php echo e($unpaidBills); ?></div>
                    <div class="text-sm text-gray-500">Belum Bayar</div>
                </div>
            </div>
        </div>
        
        <?php if(isset($isFriday) && $isFriday): ?>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-600"><?php echo e($currentWeekUnpaid); ?></div>
                    <div class="text-sm text-gray-500">Minggu Ini Belum</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-600">Rp <?php echo e(number_format($paidAmount, 0, ',', '.')); ?></div>
                    <div class="text-sm text-gray-500">Kas Masuk</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-600">Rp <?php echo e(number_format($unpaidAmount, 0, ',', '.')); ?></div>
                    <div class="text-sm text-gray-500">Tunggakan</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Pembayaran per Siswa -->
    <div class="space-y-4">
        <?php
            $studentIndex = 0;
        ?>
        <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $index = ++$studentIndex;
                $totalPaid = $payments->where('status', 'paid')->sum('amount');
                $totalBill = $payments->sum('amount');
                $totalArrears = $totalBill - $totalPaid;
                $paidCount = $payments->where('status', 'paid')->count();
                // Dynamic status based on actual weeks in month (not hardcoded 4)
                $status = $paidCount === $weeksInMonth ? 'Lunas' : ($paidCount > 0 ? 'Tunggakan' : 'Belum Lunas');
                $statusColor = $paidCount === $weeksInMonth ? 'green' : ($paidCount > 0 ? 'yellow' : 'red');
            ?>
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full font-bold text-sm mr-3"><?php echo e($index); ?></span>
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo e($payments->first()->student->name); ?></h3>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-800">Rp <?php echo e(number_format($totalBill, 0, ',', '.')); ?></div>
                        <div class="text-sm text-gray-500">Total Tagihan</div>
                    </div>
                </div>
                
                <!-- Grid untuk minggu-minggu -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                    <?php for($week = 1; $week <= $weeksInMonth; $week++): ?>
                        <?php
                            $payment = $payments->where('week_number', $week)->first();
                            $isPaid = $payment && $payment->status === 'paid';
                            $weekDate = $wednesdayDates[$week - 1] ?? null;
                            $dateLabel = $weekDate ? $weekDate->format('d M') : '';
                            // Highlight current week only if viewing current month AND it's Wednesday
                            $highlightClass = (isset($isCurrentMonth) && $isCurrentMonth && isset($isWednesday) && $isWednesday && $week == $currentWeek) ? 'ring-4 ring-red-400 bg-yellow-50' : '';
                        ?>
                        <div class="text-center p-4 border-2 rounded-lg <?php echo e($isPaid ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300'); ?> <?php echo e($highlightClass); ?>">
                            <div class="font-bold text-sm mb-1">Minggu <?php echo e($week); ?></div>
                            <?php if($dateLabel): ?>
                                <div class="text-xs text-gray-600 mb-2">Rabu, <?php echo e($dateLabel); ?></div>
                            <?php endif; ?>
                            <div class="font-bold mb-2">
                                <?php if($isPaid): ?>
                                    <span class="text-green-700">✓ Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                                <?php else: ?>
                                    <span class="text-red-700">✗ Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if(!$isPaid): ?>
                                <button onclick="console.log('Button clicked!'); showPaymentModal('<?php echo e($payment->id ?? 'new-'.$studentId.'-'.$week.'-'.$month.'-'.$year); ?>', '<?php echo e($payments->first()->student->name); ?>', <?php echo e($week); ?>, <?php echo e($studentId); ?>)" 
                                        class="inline-flex items-center justify-center w-16 h-8 rounded bg-blue-500 hover:bg-blue-600 <?php echo e($highlightClass); ?> transition-colors text-white text-xs font-bold"
                                        title="Bayar Minggu <?php echo e($week); ?> (<?php echo e($wednesdayDates[$week-1]->format('d M') ?? 'Tgl tidak diketahui'); ?>)">
                                    Bayar
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <!-- Total dan Status per siswa -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            Total: <span class="font-bold">Rp <?php echo e(number_format($totalBill, 0, ',', '.')); ?></span>
                        </span>
                        <span class="text-sm text-gray-600">
                            Lunas: <span class="font-bold text-green-700"><?php echo e($paidCount); ?>/<?php echo e($weeksInMonth); ?></span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                            <?php if($statusColor === 'green'): ?> bg-green-100 text-green-800
                            <?php elseif($statusColor === 'yellow'): ?> bg-yellow-100 text-yellow-800
                            <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                            <?php echo e($status); ?>

                        </span>
                        <?php if($totalArrears > 0): ?>
                            <button onclick="showArrearsModal(<?php echo e($studentId); ?>, '<?php echo e($payments->first()->student->name); ?>', <?php echo e($totalArrears); ?>, '<?php echo e($payments->where('status', 'unpaid')->pluck('week_number')->implode(',')); ?>')" 
                                    class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded hover:bg-red-600 transition-colors">
                                Lunasi Tunggakan
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex justify-center space-x-4 mt-8">
        <button onclick="showArrearsList()" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium shadow-sm">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Lihat Daftar Tunggakan
        </button>
        <a href="<?php echo e(route('bendahara.dashboard')); ?>" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 font-medium shadow-sm">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
</main>
</div>

<!-- Modal Daftar Tunggakan -->
<div id="arrearsListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Siswa Menunggak</h3>
        </div>
        
        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-center">
                <div class="text-sm font-semibold text-blue-800"><?php echo e($currentMonthName); ?></div>
                <div class="text-xs text-gray-600">Pembayaran kas setiap hari Rabu</div>
            </div>
            
            <!-- Total Tunggakan -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-center">
                <div class="text-2xl font-bold text-red-700">
                    Rp <?php echo e(number_format($unpaidAmount, 0, ',', '.')); ?>

                </div>
                <div class="text-sm text-red-600">Total Tunggakan Bulan Ini</div>
            </div>
            
            <!-- Daftar Siswa Menunggak -->
            <div class="space-y-4">
                <?php
                    $arrearsIndex = 0;
                ?>
                <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $unpaidPayments = $payments->where('status', 'unpaid');
                    if ($unpaidPayments->count() === 0) {
                        continue;
                    }
                    
                    $arrearsIndex++;
                    $totalArrears = $unpaidPayments->sum('amount');
                    $unpaidWeeks = $unpaidPayments->pluck('week_number');
                    ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-800 rounded-full font-bold text-xs mr-2"><?php echo e($arrearsIndex); ?></span>
                                <h3 class="text-sm font-semibold text-gray-800"><?php echo e($payments->first()->student->name); ?></h3>
                                <p class="text-xs text-gray-600 mt-1">
                                    Menunggak <?php echo e($unpaidPayments->count()); ?> minggu:<br>
                                    Minggu <?php echo e(implode(', ', $unpaidWeeks->toArray())); ?>

<?php
                                    $now = Carbon::now();
                                    $startOfMonth = Carbon::create($now->year, $now->month)->startOfMonth();
                                    $firstWednesday = $startOfMonth->copy()->next(Carbon::WEDNESDAY);
                                    foreach($unpaidWeeks as $uw) {
                                        $uwWednesday = $firstWednesday->copy()->addWeeks($uw - 1);
                                        echo '(Rabu, ' . $uwWednesday->locale('id')->isoFormat('D MMM') . ') ';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-red-700">
                                    Rp <?php echo e(number_format($totalArrears, 0, ',', '.')); ?>

                                </div>
                                <div class="text-xs text-gray-500 mb-2">Total Tunggakan</div>
                                <button class="px-3 py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-colors"
                                        onclick="showArrearsModal(<?php echo e($studentId); ?>, '<?php echo e($payments->first()->student->name); ?>', <?php echo e($totalArrears); ?>, '<?php echo e(implode(',', $unpaidWeeks->toArray())); ?>')">
                                    Lunasi Tunggakan
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            <button onclick="closeArrearsList()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Catat Pembayaran</h3>
        </div>
        
        <form id="paymentForm" class="p-6 space-y-4">
            <input type="hidden" id="payment_id" name="payment_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa:</label>
                <div id="student_name" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minggu Ke:</label>
                <div id="week_number" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah:</label>
                <div class="px-3 py-2 bg-green-100 rounded-lg text-sm font-bold text-green-700">
                    Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?>

                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran:</label>
                <input type="date" id="payment_date" name="payment_date" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan:</label>
                <input type="text" id="description" name="description" 
                       placeholder="PEMBAYARAN KAS MINGGUAN" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full">
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan Pembayaran
                </button>
                <button type="button" onclick="closePaymentModal()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pelunasan Tunggakan -->
<div id="arrearsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lunasi Tunggakan</h3>
        </div>
        
        <form id="arrearsForm" class="p-6 space-y-4">
            <input type="hidden" id="arrears_student_id" name="student_id">
            <input type="hidden" id="arrears_month" name="month">
            <input type="hidden" id="arrears_year" name="year">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa:</label>
                <div id="arrears_student_name" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minggu Tunggak:</label>
                <div id="arrears_weeks" class="px-3 py-2 bg-red-50 rounded-lg text-sm font-semibold text-red-700"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Tunggakan:</label>
                <div id="arrears_total" class="px-3 py-2 bg-red-50 rounded-lg text-lg font-bold text-red-700"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pelunasan:</label>
                <input type="date" id="arrears_date" name="payment_date" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan:</label>
                <input type="text" id="arrears_description" name="description" 
                       placeholder="PELUNASAN TUNGGAKAN KAS" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Lunasi Sekarang
                </button>
                <button type="button" onclick="closeArrearsModal()" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function processPayment(paymentId) {
    // Parse paymentId - bisa existing ID atau new-studentId-weekNumber-month-year
    if (paymentId.startsWith('new-')) {
        // Format: new-studentId-weekNumber-month-year
        const parts = paymentId.split('-');
        const studentId = parts[1];
        const weekNumber = parts[2];
        const month = parts[3];
        const year = parts[4];
        
        // Find the payment record first
        fetch(`/bendahara/api/find-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                student_id: studentId,
                week_number: weekNumber,
                month: month,
                year: year
            })
        })
        .then(response => response.json())
        .then(paymentData => {
            if (!paymentData.success) {
                showErrorToast(paymentData.message);
                return;
            }
            
            // Use the actual payment ID
            processPaymentWithTransaction(paymentData.payment.id);
        })
        .catch(error => {
            console.error('Error finding payment:', error);
            showErrorToast('Gagal menemukan data pembayaran');
        });
    } else {
        // Existing payment ID
        processPaymentWithTransaction(paymentId);
    }
}

function processPaymentWithTransaction(paymentId) {
    console.log('processPaymentWithTransaction called with paymentId:', paymentId);
    console.log('Fetching transactions from /bendahara/api/transactions');
    
    // Cek apakah ada transaksi yang tersedia
    fetch('/bendahara/api/transactions')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(transactions => {
            console.log('Transactions received:', transactions);
            console.log('Payment amount from settings:', <?php echo e($weeklyPaymentAmount); ?>);
            
            // Cari transaksi income yang belum digunakan
            const paymentAmount = <?php echo e($weeklyPaymentAmount); ?>;
            const availableTransaction = transactions.find(t => 
                t.type === 'income' && 
                t.amount === paymentAmount && 
                !t.weekly_payment_id
            );
            
            console.log('Available transaction found:', availableTransaction);
            
            if (!availableTransaction) {
                console.log('No available transaction found. Available transactions:', transactions.filter(t => t.type === 'income'));
                showWarningToast('Tidak ada transaksi pembayaran yang tersedia.\n\nLangkah yang harus dilakukan:\n1. Klik menu "Kas" di sidebar\n2. Klik "Tambah Transaksi"\n3. Pilih tipe "Pemasukan"\n4. Masukkan nominal Rp ' + paymentAmount + '\n5. Simpan transaksi\n6. Kembali ke halaman ini dan coba lagi', 'Transaksi Tidak Tersedia', 10000);
                return;
            }
            
            // Proses pembayaran
            fetch('/bendahara/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    payment_id: paymentId,
                    transaction_id: availableTransaction.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessToast('Pembayaran berhasil dicatat!');
                    location.reload();
                } else {
                    showErrorToast(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorToast('Terjadi kesalahan saat memproses pembayaran');
            });
        })
        .catch(error => {
            console.error('Error fetching transactions:', error);
            console.error('Error details:', error.message);
            showErrorToast('Gagal mengambil data transaksi: ' + error.message);
        });
}
</script>

<script>
// Payment Modal Functions
function showPaymentModal(paymentId, studentName, week, studentId) {
    console.log('Opening payment modal for:', {paymentId, studentName, week, studentId});
    console.log('Parameters:', paymentId, studentName, week, studentId);
    
    // Check if modal exists
    const modal = document.getElementById('paymentModal');
    console.log('Modal element:', modal);
    
    if (!modal) {
        console.error('Payment modal not found!');
        showErrorToast('Modal tidak ditemukan!');
        return;
    }
    
    // Set form values
    const paymentIdElement = document.getElementById('payment_id');
    const studentNameElement = document.getElementById('student_name');
    const weekNumberElement = document.getElementById('week_number');
    const paymentDateElement = document.getElementById('payment_date');
    const descriptionElement = document.getElementById('description');
    
    console.log('Form elements:', {
        paymentIdElement,
        studentNameElement,
        weekNumberElement,
        paymentDateElement,
        descriptionElement
    });
    
    if (paymentIdElement) paymentIdElement.value = paymentId;
    if (paymentIdElement) paymentIdElement.dataset.studentId = studentId;
    if (studentNameElement) studentNameElement.textContent = studentName;
    if (weekNumberElement) weekNumberElement.textContent = week;
    
    // Set current date and default description
    const today = new Date().toISOString().split('T')[0];
    if (paymentDateElement) paymentDateElement.value = today;
    
    // Get month name in uppercase
    const monthNames = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 
                       'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
    const urlParams = new URLSearchParams(window.location.search);
    const currentMonth = parseInt(urlParams.get('month')) || new Date().getMonth() + 1;
    const currentYear = parseInt(urlParams.get('year')) || new Date().getFullYear();
    const monthName = monthNames[currentMonth - 1];
    
    if (descriptionElement) descriptionElement.value = `PEMBAYARAN KAS MINGGU ${week} ${monthName} ${currentYear} - ${studentName}`;
    
    console.log('Form values set, showing modal...');
    
    // Show modal
    modal.classList.remove('hidden');
    console.log('Modal classes after remove:', modal.className);
    
    // Check if modal is visible
    setTimeout(() => {
        const isVisible = !modal.classList.contains('hidden');
        console.log('Modal visibility check:', isVisible);
        if (!isVisible) {
            console.error('Modal still hidden after removeClass!');
        }
    }, 100);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.getElementById('paymentForm').reset();
    }
}

// Handle payment form submission
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Payment form submission started');
    
    const paymentId = document.getElementById('payment_id').value;
    const paymentDate = document.getElementById('payment_date').value;
    const description = document.getElementById('description').value;
    const month = <?php echo e($month); ?>;
    const year = <?php echo e($year); ?>;
    const weekNumber = document.getElementById('week_number').textContent;
    
    console.log('Form data:', {paymentId, paymentDate, description});
    
    // Get student_id from the payment data
    const studentId = document.getElementById('payment_id').dataset.studentId;
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'MEMPROSES...';
    submitBtn.disabled = true;
    
    // Create transaction first, then process payment
    fetch('/bendahara/kas/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            student_id: studentId,
            type: 'income',
            date: paymentDate,
            description: description,
            week_number: weekNumber,
            month: month,
            year: year
        })
    })
    .then(response => {
        console.log('Transaction response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Transaction response:', data);
        
        if (data.success && data.transaction) {
            // Process payment with the created transaction
            return fetch('/bendahara/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_id: paymentId,
                    transaction_id: data.transaction.id
                })
            });
        } else {
            throw new Error(data.message || 'Transaksi gagal');
        }
    })
    .then(response => {
        console.log('Payment response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Payment response:', data);
        
        if (data.success) {
            showSuccessToast('Pembayaran berhasil dicatat!');
            closePaymentModal();
            location.reload();
        } else {
            showErrorToast('Gagal memproses pembayaran: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Full error:', error);
        showErrorToast('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        // Reset button
        if (submitBtn && originalText) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
});

// Close modal when clicking outside
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
        closeArrearsModal();
        closeArrearsList();
    }
});


// Arrears List Modal Functions
function showArrearsList() {
    console.log('Opening arrears list modal');
    const modal = document.getElementById('arrearsListModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeArrearsList() {
    const modal = document.getElementById('arrearsListModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Arrears Payment Modal Functions
function showArrearsModal(studentId, studentName, totalArrears, weeks) {
    console.log('Opening arrears modal for:', {studentId, studentName, totalArrears, weeks});
    
    const modal = document.getElementById('arrearsModal');
    if (!modal) {
        console.error('Arrears modal not found!');
        showErrorToast('Modal tidak ditemukan!');
        return;
    }
    
    // Set form values
    document.getElementById('arrears_student_id').value = studentId;
    document.getElementById('arrears_student_name').textContent = studentName;
    document.getElementById('arrears_total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalArrears);
    
    // Set month and year from URL or current date
    const urlParams = new URLSearchParams(window.location.search);
    const currentMonth = parseInt(urlParams.get('month')) || new Date().getMonth() + 1;
    const currentYear = parseInt(urlParams.get('year')) || new Date().getFullYear();
    
    document.getElementById('arrears_month').value = currentMonth;
    document.getElementById('arrears_year').value = currentYear;
    
    // Format weeks
    const weeksArray = weeks.split(',').map(w => 'Minggu ' + w.trim()).join(', ');
    document.getElementById('arrears_weeks').textContent = weeksArray;
    
    // Set date and description
    document.getElementById('arrears_date').value = new Date().toISOString().split('T')[0];
    
    // Get month name in uppercase
    const monthNames = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 
                       'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
    const monthName = monthNames[currentMonth - 1];
    
    document.getElementById('arrears_description').value = `PELUNASAN TUNGGAKAN KAS ${monthName} ${currentYear} - ${weeksArray}`;
    
    // Show modal
    modal.classList.remove('hidden');
    console.log('Arrears modal should be visible now');
}

function closeArrearsModal() {
    const modal = document.getElementById('arrearsModal');
    if (modal) {
        modal.classList.add('hidden');
        document.getElementById('arrearsForm').reset();
    }
}

// Handle arrears form submission
document.getElementById('arrearsForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Arrears form submission started');
    
    const studentId = document.getElementById('arrears_student_id')?.value;
    const paymentDate = document.getElementById('arrears_date')?.value;
    const description = document.getElementById('arrears_description')?.value;
    const totalAmount = parseInt(document.getElementById('arrears_total')?.textContent?.replace(/[^\d]/g, '') || '0');
    
    console.log('Arrears form data:', {studentId, paymentDate, description, totalAmount});
    
    if (!studentId || !paymentDate || totalAmount === 0) {
        showWarningToast('Data form tidak lengkap!');
        return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn?.textContent;
    if (submitBtn) {
        submitBtn.textContent = 'MEMPROSES...';
        submitBtn.disabled = true;
    }
    
    // Get month and year from hidden fields
    const currentMonth = parseInt(document.getElementById('arrears_month')?.value) || new Date().getMonth() + 1;
    const currentYear = parseInt(document.getElementById('arrears_year')?.value) || new Date().getFullYear();
    
    console.log('Using month/year from form:', {currentMonth, currentYear});
    
    // Create transaction
    fetch('/bendahara/kas/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            student_id: studentId,
            type: 'income',
            amount: totalAmount,
            date: paymentDate,
            description: description
        })
    })
    .then(response => {
        console.log('Transaction response:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Transaction data:', data);
        
        if (data.success && data.transaction) {
            // Process arrears
            return fetch('/bendahara/api/process-arrears', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    transaction_id: data.transaction.id,
                    month: currentMonth,
                    year: currentYear
                })
            });
        } else {
            throw new Error(data.message || 'Transaksi gagal');
        }
    })
    .then(response => {
        console.log('Arrears response:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Arrears data:', data);
        
        if (data.success) {
            showSuccessToast('Tunggakan berhasil dilunasi!');
            closeArrearsModal();
            closeArrearsList();
            location.reload();
        } else {
            showErrorToast('Gagal melunasi tunggakan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Arrears error:', error);
        showErrorToast('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        // Reset button
        if (submitBtn && originalText) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
});

// Close modals when clicking outside
document.getElementById('arrearsListModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeArrearsList();
    }
});

document.getElementById('arrearsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeArrearsModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<!-- Dashboard CSS -->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
.sidebar-header { padding: 24px 20px; border-bottom: 1px solid #e2e8f0; }
.logo { display: flex; align-items: center; gap: 12px; }
.logo-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
.logo-text { font-size: 20px; font-weight: 700; color: #1e293b; }
.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #64748b; text-decoration: none; transition: all 0.2s ease; border-radius: 0 8px 8px 0; margin: 0 12px; }
.nav-item:hover { background: #f8fafc; color: #3b82f6; }
.nav-item.active { background: #eff6ff; color: #3b82f6; font-weight: 600; }
.nav-icon { width: 20px; height: 20px; }
.sidebar-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; }
.user-profile-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.user-avatar-mini { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }
.user-name-mini { font-size: 13px; font-weight: 600; color: #1e293b; }
.user-role-mini { font-size: 11px; color: #64748b; }
.logout-form { display: block; }
.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.logout-btn:hover { background: #fecaca; }
.logout-icon { width: 16px; height: 16px; }
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.main-content { flex: 1; padding: 32px; overflow-y: auto; }
.greeting-section { margin-bottom: 32px; }
.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.greeting-subtitle { font-size: 16px; color: #64748b; }
.stats-section, .feature-cards { margin-bottom: 32px; }
.stats-grid, .feature-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
.stat-card, .feature-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.feature-icon, .stat-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; }
.feature-icon svg, .stat-icon svg { width: 32px; height: 32px; }
.feature-icon.green { background: #dcfce7; color: #10b981; }
.feature-icon.orange { background: #fed7aa; color: #f97316; }
.feature-icon.blue { background: #dbeafe; color: #3b82f6; }
.stat-icon.balance { background: #dbeafe; color: #3b82f6; }
.stat-icon.income { background: #dcfce7; color: #10b981; }
.stat-icon.expense { background: #fee2e2; color: #ef4444; }
.stat-icon.payment { background: #e0e7ff; color: #6366f1; }
.stat-icon.remaining { background: #fef3c7; color: #f59e0b; }
.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.stat-description { font-size: 14px; color: #64748b; }
.tables-section { margin-bottom: 32px; }
.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }
.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }
.table-title { font-size: 20px; font-weight: 600; color: #1e293b; }
.table-container { padding: 24px; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; color: #475569; padding: 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
.data-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.data-table tr:hover td { background: #f8fafc; }
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.status-badge.success { background: #dcfce7; color: #166534; }
.status-badge.danger { background: #fee2e2; color: #dc2626; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.nav-btn { display: flex; align-items: center; gap: 8px; background: #3b82f6; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s ease; }
.nav-btn:hover { background: #2563eb; transform: translateY(-1px); }
.action-btn { display: inline-flex; align-items: center; gap: 4px; background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.action-btn:hover { background: #059669; transform: translateY(-1px); }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .sidebar { width: 260px; } .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } .tables-section { grid-template-columns: 1fr; } }
</style>

        </main>
    </div>
</div>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/weekly-payments.blade.php ENDPATH**/ ?>
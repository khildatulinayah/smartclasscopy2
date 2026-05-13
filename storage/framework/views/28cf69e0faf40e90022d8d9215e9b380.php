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
    
    <!-- Search Box with Month Navigation -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="searchInput" placeholder="Cari nama siswa... (Ctrl+F)" 
                       title="Tekan Ctrl+F untuk fokus search, ESC untuk clear"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       onkeyup="filterStudents()">
            </div>
            <button onclick="clearSearch()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Clear
            </button>
            
            <!-- Month Navigation with Dropdown and Arrows -->
            <div class="flex items-center space-x-2 pl-4 border-l border-gray-200">
                <!-- Arrow Navigation -->
                <a href="?month=<?php echo e($prevMonth); ?>&year=<?php echo e($prevYear); ?>" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors" title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <!-- Dropdown Navigation -->
                <form id="monthForm" method="GET" class="flex items-center space-x-2">
                    <select name="month" id="monthSelect" onchange="this.form.submit()" 
                            class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold text-sm border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                        <?php for($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                                <?php echo e(Carbon::create()->month($m)->locale('id')->translatedFormat('F')); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="year" id="yearSelect" onchange="this.form.submit()" 
                            class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold text-sm border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                        <?php for($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>>
                                <?php echo e($y); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                    <!-- Hidden inputs to preserve other GET parameters -->
                    <?php $__currentLoopData = request()->except(['month', 'year']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </form>
                
                <!-- Arrow Navigation -->
                <a href="?month=<?php echo e($nextMonth); ?>&year=<?php echo e($nextYear); ?>" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors" title="Bulan Selanjutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        <div id="searchResults" class="mt-2 text-sm text-gray-600"></div>
    </div>
    
    <!-- Daftar Pembayaran per Siswa -->
    <div id="studentsList" class="space-y-4">
        <?php
            $studentIndex = 0;
        ?>
        <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $index = ++$studentIndex;
                $totalPaid = $payments->where('status', 'paid')->count() * $weeklyPaymentAmount;
                
                // Hanya hitung tagihan untuk hari Rabu yang sudah lewat
                $eligibleWeeks = 0;
                $unpaidAmount = 0;
                $now = Carbon::now();
                
                foreach($payments as $payment) {
                    // Cek tanggal Rabu untuk minggu ini
                    $wednesdayDate = isset($wednesdayDates[$payment->week_number - 1]) 
                        ? $wednesdayDates[$payment->week_number - 1] 
                        : null;
                    
                    // Hanya hitung jika Rabu sudah lewat atau bukan bulan sekarang
                    if ($wednesdayDate && ($wednesdayDate->lt($now) || $month != $now->month || $year != $now->year)) {
                        $eligibleWeeks++;
                        if ($payment->status === 'unpaid') {
                            $unpaidAmount += $payment->amount;
                        }
                    }
                }
                
                $totalBill = $eligibleWeeks * $weeklyPaymentAmount;
                $totalArrears = $unpaidAmount;
                $paidCount = $payments->where('status', 'paid')->count();
                
                // Dynamic status based on eligible weeks (not total weeks in month)
                $status = $totalArrears === 0 ? 'Lunas' : ($paidCount > 0 ? 'Tunggakan' : 'Belum Lunas');
                $statusColor = $totalArrears === 0 ? 'green' : ($paidCount > 0 ? 'yellow' : 'red');
            ?>
            
            <div class="student-card bg-white rounded-xl shadow-sm p-6 border border-gray-100" data-student-id="<?php echo e($studentId); ?>" data-student-name="<?php echo e(strtolower($payments->first()->student->name)); ?>">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-800 rounded-full font-bold text-sm mr-3"><?php echo e($index); ?></span>
                        <h3 class="text-lg font-semibold text-gray-800 student-name"><?php echo e($payments->first()->student->name); ?></h3>
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
                            $now = Carbon::now();
                            
                            // Tentukan warna card berdasarkan kondisi
                            if ($isPaid) {
                                $cardClass = 'bg-green-50 border-green-300';
                                $textClass = 'text-green-700';
                            } else {
                                // Cek apakah Rabu sudah lewat
                                $wednesdayPassed = $weekDate && ($weekDate->lt($now) || $month != $now->month || $year != $now->year);
                                
                                if ($wednesdayPassed) {
                                    // Rabu sudah lewat = tunggakan (merah)
                                    $cardClass = 'bg-red-50 border-red-300';
                                    $textClass = 'text-red-700';
                                } else {
                                    // Rabu belum lewat = belum waktunya (abu-abu)
                                    $cardClass = 'bg-gray-50 border-gray-300';
                                    $textClass = 'text-gray-700';
                                }
                            }
                            
                            // Highlight current week only if viewing current month AND it's Wednesday
                            $highlightClass = (isset($isCurrentMonth) && $isCurrentMonth && isset($isWednesday) && $isWednesday && $week == $currentWeek) ? 'ring-4 ring-yellow-400 bg-yellow-50' : '';
                        ?>
                        <div class="text-center p-4 border-2 rounded-lg <?php echo e($cardClass); ?> <?php echo e($highlightClass); ?>" data-week="<?php echo e($week); ?>">
                            <div class="font-bold text-sm mb-1">Minggu <?php echo e($week); ?></div>
                            <?php if($dateLabel): ?>
                                <div class="text-xs text-gray-600 mb-2">Rabu, <?php echo e($dateLabel); ?></div>
                            <?php endif; ?>
                            <div class="font-bold mb-2">
                                <?php if($isPaid): ?>
                                    <span class="<?php echo e($textClass); ?>">✓ Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?></span>
                                <?php else: ?>
                                    <span class="<?php echo e($textClass); ?>">✗ Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if(!$isPaid): ?>
                                <?php
                                    // Tentukan warna tombol berdasarkan kondisi
                                    if ($wednesdayPassed) {
                                        // Rabu sudah lewat = tunggakan (tombol merah)
                                        $buttonClass = 'bg-red-500 hover:bg-red-600';
                                    } else {
                                        // Rabu belum lewat = belum waktunya (tombol hijau)
                                        $buttonClass = 'bg-green-500 hover:bg-green-600';
                                    }
                                ?>
                                <button onclick="console.log('Button clicked!'); showPaymentModal('<?php echo e($payment->id ?? 'new-'.$studentId.'-'.$week.'-'.$month.'-'.$year); ?>', '<?php echo e($payments->first()->student->name); ?>', <?php echo e($week); ?>, <?php echo e($studentId); ?>)" 
                                        class="inline-flex items-center justify-center w-16 h-8 rounded <?php echo e($buttonClass); ?> <?php echo e($highlightClass); ?> transition-colors text-white text-xs font-bold"
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
                            Lunas: <span class="font-bold text-green-700"><?php echo e($paidCount); ?>/<?php echo e($eligibleWeeks); ?></span>
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
                <?php
                    // Hitung total tunggakan hanya untuk Rabu yang sudah lewat
                    $totalEligibleArrears = 0;
                    $now = Carbon::now();
                    
                    foreach($paymentsByStudent as $studentId => $payments) {
                        foreach($payments as $payment) {
                            if ($payment->status === 'unpaid') {
                                // Cek tanggal Rabu untuk minggu ini
                                $wednesdayDate = isset($wednesdayDates[$payment->week_number - 1]) 
                                    ? $wednesdayDates[$payment->week_number - 1] 
                                    : null;
                                
                                // Hanya hitung jika Rabu sudah lewat atau bukan bulan sekarang
                                if ($wednesdayDate && ($wednesdayDate->lt($now) || $month != $now->month || $year != $now->year)) {
                                    $totalEligibleArrears += $payment->amount;
                                }
                            }
                        }
                    }
                ?>
                <div class="text-2xl font-bold text-red-700">
                    Rp <?php echo e(number_format($totalEligibleArrears, 0, ',', '.')); ?>

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
                    // Hanya hitung tunggakan untuk hari Rabu yang sudah lewat
                    $eligibleUnpaidPayments = collect();
                    $now = Carbon::now();
                    
                    foreach($payments as $payment) {
                        if ($payment->status === 'unpaid') {
                            // Cek tanggal Rabu untuk minggu ini
                            $wednesdayDate = isset($wednesdayDates[$payment->week_number - 1]) 
                                ? $wednesdayDates[$payment->week_number - 1] 
                                : null;
                            
                            // Hanya hitung jika Rabu sudah lewat atau bukan bulan sekarang
                            if ($wednesdayDate && ($wednesdayDate->lt($now) || $month != $now->month || $year != $now->year)) {
                                $eligibleUnpaidPayments->push($payment);
                            }
                        }
                    }
                    
                    if ($eligibleUnpaidPayments->count() === 0) {
                        continue;
                    }
                    
                    $arrearsIndex++;
                    $totalArrears = $eligibleUnpaidPayments->sum('amount');
                    $unpaidWeeks = $eligibleUnpaidPayments->pluck('week_number');
                    ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-800 rounded-full font-bold text-xs mr-2"><?php echo e($arrearsIndex); ?></span>
                                <h3 class="text-sm font-semibold text-gray-800"><?php echo e($payments->first()->student->name); ?></h3>
                                <p class="text-xs text-gray-600 mt-1">
                                    Menunggak <?php echo e($eligibleUnpaidPayments->count()); ?> minggu:<br>
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
// Search Functions
function filterStudents() {
    const searchInput = document.getElementById('searchInput');
    const searchValue = searchInput.value.toLowerCase().trim();
    const studentCards = document.querySelectorAll('.student-card');
    const searchResults = document.getElementById('searchResults');
    let visibleCount = 0;
    let totalStudents = studentCards.length;

    studentCards.forEach(card => {
        const studentName = card.dataset.studentName;
        const nameElement = card.querySelector('.student-name');
        const originalName = nameElement.textContent;
        
        if (studentName.includes(searchValue)) {
            card.style.display = 'block';
            visibleCount++;
            
            // Highlight search text
            if (searchValue.length > 0) {
                nameElement.innerHTML = highlightSearchText(originalName, searchInput.value);
            } else {
                nameElement.textContent = originalName;
            }
        } else {
            card.style.display = 'none';
            nameElement.textContent = originalName; // Reset highlight when hidden
        }
    });

    // Update search results info
    if (searchValue.length > 0) {
        searchResults.innerHTML = `Menampilkan <span class="font-semibold">${visibleCount}</span> dari <span class="font-semibold">${totalStudents}</span> siswa`;
        if (visibleCount === 0) {
            searchResults.innerHTML = `<span class="text-red-600 font-semibold">Tidak ada siswa dengan nama "${searchInput.value}"</span>`;
        }
    } else {
        searchResults.textContent = '';
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const studentCards = document.querySelectorAll('.student-card');

    searchInput.value = '';
    searchResults.textContent = '';
    
    studentCards.forEach(card => {
        card.style.display = 'block';
        // Reset highlight
        const nameElement = card.querySelector('.student-name');
        if (nameElement) {
            nameElement.textContent = nameElement.textContent; // Remove HTML tags
        }
    });

    // Focus back to search input
    searchInput.focus();
}

// Keyboard shortcut for search (Ctrl+F or Cmd+F)
document.addEventListener('keydown', function(e) {
    // Ctrl+F or Cmd+F for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
        document.getElementById('searchInput').select();
    }
    
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('searchInput');
        if (document.activeElement === searchInput) {
            clearSearch();
        }
    }
});

// Highlight search text in student names
function highlightSearchText(text, searchValue) {
    if (!searchValue) return text;
    
    const regex = new RegExp(`(${searchValue})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
}

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
            // Tetap fokus pada siswa yang baru dibayar
            scrollToStudent(studentId);
            updateStudentUI(studentId, weekNumber);
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

// Fungsi untuk scroll ke siswa yang baru dibayar
function scrollToStudent(studentId) {
    console.log('Scrolling to student:', studentId);
    
    // Cari container siswa berdasarkan student ID
    setTimeout(() => {
        // Cari elemen siswa dengan data attribute yang sesuai
        const studentElements = document.querySelectorAll('[data-student-id]');
        let targetElement = null;
        
        studentElements.forEach(element => {
            if (element.dataset.studentId == studentId) {
                targetElement = element;
            }
        });
        
        if (targetElement) {
            // Scroll ke elemen dengan smooth behavior
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            // Tambahkan highlight effect
            targetElement.classList.add('payment-success-highlight');
            
            // Hapus highlight setelah 3 detik
            setTimeout(() => {
                targetElement.classList.remove('payment-success-highlight');
            }, 3000);
            
            console.log('Successfully scrolled to student element');
        } else {
            console.warn('Student element not found for ID:', studentId);
        }
    }, 500); // Delay sedikit untuk memastikan DOM siap
}

// Fungsi untuk update UI siswa setelah pembayaran
function updateStudentUI(studentId, weekNumber) {
    console.log('Updating UI for student:', studentId, 'week:', weekNumber);
    
    // Cari container siswa
    const studentContainer = document.querySelector(`[data-student-id="${studentId}"]`);
    if (studentContainer) {
        // Cari card minggu yang dibayar
        const weekCard = studentContainer.querySelector(`[data-week="${weekNumber}"]`);
        if (weekCard) {
            // Ubah warna card dari merah menjadi hijau
            weekCard.classList.remove('bg-red-50', 'border-red-300');
            weekCard.classList.add('bg-green-50', 'border-green-300');
            
            // Cari tombol bayar dalam card tersebut
            const weekButton = weekCard.querySelector('button');
            if (weekButton) {
                // Ganti tombol dengan indikator berhasil
                weekButton.innerHTML = '✓ Lunas';
                weekButton.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                weekButton.classList.add('bg-green-500', 'cursor-default');
                weekButton.disabled = true;
                weekButton.onclick = null;
            }
            
            // Cari amount text dan ubah warnanya
            const amountText = weekCard.querySelector('.text-red-700');
            if (amountText) {
                amountText.classList.remove('text-red-700');
                amountText.classList.add('text-green-700');
                amountText.innerHTML = '✓ Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?>';
            }
        }
        
        // Update status badge jika ada
        const statusBadge = studentContainer.querySelector('.status-badge');
        if (statusBadge) {
            // Hitung ulang status (ini akan diupdate oleh server saat refresh)
            console.log('Status badge found, will be updated on next refresh');
        }
    }
}
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

/* Payment Success Highlight */
.payment-success-highlight {
    animation: paymentSuccessPulse 2s ease-in-out;
    border-color: #10b981 !important;
    background-color: #f0fdf4 !important;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3) !important;
}

@keyframes paymentSuccessPulse {
    0% {
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
        transform: scale(1.02);
    }
    100% {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        transform: scale(1);
    }
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Update button styles for paid status */
.bg-green-500.cursor-default {
    cursor: default !important;
    opacity: 0.8;
}
</style>

        </main>
    </div>
</div>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/weekly-payments.blade.php ENDPATH**/ ?>
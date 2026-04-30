
<?php use Carbon\Carbon; ?>

<?php $__env->startSection('title', 'Tracking Pembayaran Mingguan'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-layout">
   
<?php echo $__env->make('components.bendahara-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-area">
        <main class="main-content">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pembayaran Mingguan</h1>
                <p class="text-gray-600">Kelola dan pantau pembayaran kas siswa per minggu</p>
            </div>
    
    <!-- Month Navigation -->
    <div class="flex items-center justify-center mb-8 space-x-4">
        <a href="?month=<?php echo e($prevMonth); ?>&year=<?php echo e($prevYear); ?>" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium">
            ← Bulan Sebelumnya
        </a>
        <div class="bg-blue-500 text-white px-8 py-4 rounded-xl shadow-lg">
            <div class="text-xl font-bold"><?php echo e($currentMonthName); ?></div>
        </div>
        <a href="?month=<?php echo e($nextMonth); ?>&year=<?php echo e($nextYear); ?>" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium">
            Bulan Selanjutnya →
        </a>
    </div>
    
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
    
    
    <?php if(isset($isWednesday) && $isWednesday): ?>
        <div class="bg-red-500 text-white rounded-xl shadow-lg p-6 mb-8 text-center">
            <div class="flex items-center justify-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h2 class="text-xl font-bold">HARI RABU - PEMBAYARAN KAS!</h2>
            </div>
            <p class="text-lg">Prioritaskan <strong><?php echo e($currentWeekUnpaid); ?></strong> siswa untuk Minggu ke-<?php echo e($currentWeek); ?></p>
        </div>
    <?php else: ?>
        <div class="bg-yellow-100 border border-yellow-200 rounded-xl shadow-md p-6 mb-8 text-center">
            <div class="flex items-center justify-center mb-2">
                <svg class="w-6 h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-lg font-semibold text-yellow-800">Selanjutnya: Hari Rabu</h2>
            </div>
            <p class="text-gray-700">Rabu, <?php echo e($nextWednesday ?? 'Minggu ini'); ?> | <?php echo e($currentWeekUnpaid ?? 0); ?> belum bayar minggu ini</p>
        </div>
    <?php endif; ?>
    
    <!-- Tabel Pembayaran -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pembayaran Siswa</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu 1</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu 2</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu 3</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu 4</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bayar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tunggakan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $totalPaid = $payments->where('status', 'paid')->sum('amount');
                            $totalBill = $payments->sum('amount');
                            $totalArrears = $totalBill - $totalPaid;
                            $paidCount = $payments->where('status', 'paid')->count();
                            $status = $paidCount === 4 ? 'Lunas' : ($paidCount > 0 ? 'Tunggakan' : 'Belum Lunas');
                            $statusColor = $paidCount === 4 ? 'green' : ($paidCount > 0 ? 'yellow' : 'red');
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($payments->first()->student->name); ?></div>
                            </td>
                            <?php for($week = 1; $week <= 4; $week++): ?>
                                <?php
                                    $payment = $payments->where('week_number', $week)->first();
                                    $isPaid = $payment && $payment->status === 'paid';
                                    $highlightClass = (isset($isWednesday) && $isWednesday && $week == $currentWeek) ? 'ring-2 ring-red-400 bg-red-50' : '';
                                ?>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-8 rounded <?php echo e($isPaid ? 'bg-green-100' : 'bg-red-100'); ?> <?php echo e($highlightClass); ?>">
                                        <?php if($isPaid): ?>
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endfor; ?>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                Rp <?php echo e(number_format($totalBill, 0, ',', '.')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                Rp <?php echo e(number_format($totalPaid, 0, ',', '.')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                Rp <?php echo e(number_format($totalArrears, 0, ',', '.')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php if($statusColor === 'green'): ?> bg-green-100 text-green-800
                                    <?php elseif($statusColor === 'yellow'): ?> bg-yellow-100 text-yellow-800
                                    <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                    <?php echo e($status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <?php if($totalArrears > 0): ?>
                                    <button onclick="showArrearsModal(<?php echo e($studentId); ?>, '<?php echo e($payments->first()->student->name); ?>', <?php echo e($totalArrears); ?>, '<?php echo e($payments->where('status', 'unpaid')->pluck('week_number')->implode(',')); ?>')" 
                                            class="text-red-600 hover:text-red-900 font-medium">
                                        Lihat Tunggakan
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400">Lunas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
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
                <?php $__currentLoopData = $paymentsByStudent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $studentId => $payments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $unpaidPayments = $payments->where('status', 'unpaid');
                    if ($unpaidPayments->count() === 0) {
                        continue;
                    }
                    
                    $totalArrears = $unpaidPayments->sum('amount');
                    $unpaidWeeks = $unpaidPayments->pluck('week_number');
                    ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
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

<!-- Modal Pelunasan Tunggakan -->
<div id="arrearsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lunasi Tunggakan</h3>
        </div>
        
        <form id="arrearsForm" class="p-6 space-y-4">
            <input type="hidden" id="arrears_student_id" name="student_id">
            
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
                       placeholder="Pelunasan tunggakan kas" 
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
    // Cek apakah ada transaksi yang tersedia
    fetch('/bendahara/api/transactions')
        .then(response => response.json())
        .then(transactions => {
            // Cari transaksi income yang belum digunakan
            const availableTransaction = transactions.find(t => 
                t.type === 'income' && 
                t.amount === 5000 && 
                !t.weekly_payment_id
            );
            
            if (!availableTransaction) {
                alert('Tidak ada transaksi pembayaran yang tersedia. Silahkan input transaksi kas terlebih dahulu.');
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
                    alert('Pembayaran berhasil dicatat!');
                    location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran');
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data transaksi');
        });
}
</script>

<script>
// Simple modal functions
function showPaymentModal(paymentId, studentName, week, studentId) {
    console.log('Opening modal for:', paymentId, studentName, week, studentId);
    
    // Set form values
    document.getElementById('payment_id').value = paymentId;
    document.getElementById('payment_id').dataset.studentId = studentId;
    document.getElementById('student_name').textContent = studentName;
    document.getElementById('week_number').textContent = week;
    
    // Set current date and default description
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('payment_date').value = today;
    document.getElementById('description').value = `Pembayaran kas Minggu ${week} - ${studentName}`;
    
    // Show modal
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal should be visible now');
    } else {
        console.error('Modal not found!');
        alert('Modal tidak ditemukan!');
    }
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.getElementById('paymentForm').reset();
    }
}

// Form submission with simplified approach
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Form submission started');
    
    const paymentId = document.getElementById('payment_id').value;
    const paymentDate = document.getElementById('payment_date').value;
    const description = document.getElementById('description').value;
    
    console.log('Form data:', {paymentId, paymentDate, description});
    
    // Get student_id from the payment data
    const studentId = document.getElementById('payment_id').dataset.studentId || 1;
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'MEMPROSES...';
    submitBtn.disabled = true;
    
    // Create transaction
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
            amount: 5000,
            date: paymentDate,
            description: description
        })
    })
    .then(response => {
        console.log('Transaction response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Transaction response:', data);
        
        if (data.success && data.transaction) {
            // Process payment
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
            alert('Pembayaran berhasil dicatat!');
            closePaymentModal();
            location.reload();
        } else {
            alert('Gagal memproses pembayaran: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Full error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
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
        alert('Modal tidak ditemukan!');
        return;
    }
    
    // Set form values
    document.getElementById('arrears_student_id').value = studentId;
    document.getElementById('arrears_student_name').textContent = studentName;
    document.getElementById('arrears_total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalArrears);
    
    // Format weeks
    const weeksArray = weeks.split(',').map(w => 'Minggu ' + w.trim()).join(', ');
    document.getElementById('arrears_weeks').textContent = weeksArray;
    
    // Set date and description
    document.getElementById('arrears_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('arrears_description').value = `Pelunasan tunggakan kas - ${weeksArray}`;
    
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
        alert('Data form tidak lengkap!');
        return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn?.textContent;
    if (submitBtn) {
        submitBtn.textContent = 'MEMPROSES...';
        submitBtn.disabled = true;
    }
    
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
                    transaction_id: data.transaction.id
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
            alert('Tunggakan berhasil dilunasi!');
            closeArrearsModal();
            closeArrearsList();
            location.reload();
        } else {
            alert('Gagal melunasi tunggakan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Arrears error:', error);
        alert('Terjadi kesalahan: ' + error.message);
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
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/weekly-payments.blade.php ENDPATH**/ ?>
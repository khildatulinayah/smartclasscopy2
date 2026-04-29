@extends('layouts.app')
<?php use Carbon\Carbon; ?>

@section('title', 'Tracking Pembayaran Mingguan')

@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="pixel-font text-center text-yellow-400 mb-8" style="font-size: 20px; letter-spacing: 2px;">~ TRACKING PEMBAYARAN MINGGUAN ~</h2>
    
    <!-- Month Navigation -->
    <div class="flex items-center justify-center mb-8 space-x-4 px-4">
        <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="pixel-button px-6 py-3 bg-gray-400 text-black hover:bg-gray-500 font-bold shadow-lg transform hover:scale-105 transition-all">
            << Bulan Sebelumnya
        </a>
        <div class="pixel-font text-2xl md:text-3xl font-bold bg-blue-500 text-white px-12 py-6 border-6 border-black shadow-2xl rounded-lg">
            {{ $currentMonthName }}
        </div>
        <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="pixel-button px-6 py-3 bg-gray-400 text-black hover:bg-gray-500 font-bold shadow-lg transform hover:scale-105 transition-all">
            Bulan Selanjutnya >>
        </a>
    </div>
    
    <!-- Statistik -->
    <div class="pixel-card p-4 mb-6 bg-blue-200">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
            <div>
                <div class="pixel-font text-lg font-bold">{{ $totalStudents }}</div>
                <div class="pixel-font text-xs">Total Siswa</div>
            </div>
            <div>
                <div class="pixel-font text-lg font-bold">{{ $totalBills }}</div>
                <div class="pixel-font text-xs">Total Tagihan</div>
            </div>
            <div>
                <div class="pixel-font text-lg font-bold text-green-700">{{ $paidBills }}</div>
                <div class="pixel-font text-xs">Sudah Bayar</div>
            </div>
            <div>
                <div class="pixel-font text-lg font-bold text-red-700">{{ $unpaidBills }}</div>
                <div class="pixel-font text-xs">Belum Bayar</div>
            </div>
            @if(isset($isFriday) && $isFriday)
            <div>
                <div class="pixel-font text-lg font-bold text-red-500 animate-pulse">{{ $currentWeekUnpaid }}</div>
                <div class="pixel-font text-xs">Minggu Ini Belum</div>
            </div>
            @endif
            <div>
                <div class="pixel-font text-lg font-bold">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                <div class="pixel-font text-xs">Kas Masuk</div>
            </div>
            <div>
                <div class="pixel-font text-lg font-bold">Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</div>
                <div class="pixel-font text-xs">Tunggakan</div>
            </div>
        </div>
    </div>

    {{-- Banner Dinamis Hari Jumat --}}
@if(isset($isWednesday) && $isWednesday)
    <div class="pixel-card p-6 mb-6 bg-red-500 text-white text-center border-4 border-black shadow-2xl">
        <h2 class="pixel-font text-2xl font-bold mb-2 animate-bounce">🚨 HARI RABU - PEMBAYARAN KAS!</h2>
        <p class="text-lg">Prioritaskan <strong>{{ $currentWeekUnpaid }}</strong> siswa untuk Minggu ke-{{ $currentWeek }}</p>
    </div>
@else
    <div class="pixel-card p-6 mb-6 bg-yellow-400 text-black text-center border-4 border-black shadow-xl">
<h2 class="pixel-font text-xl font-bold mb-2">⏳ Selanjutnya: Hari Rabu</h2>
<p class="text-lg">Rabu, {{ $nextWednesday ?? 'Minggu ini' }} | {{ $currentWeekUnpaid ?? 0 }} belum bayar minggu ini</p>
    </div>
    @endif
    
    <!-- Daftar Pembayaran per Siswa -->
    <div class="pixel-card p-6">
        @foreach($paymentsByStudent as $studentId => $payments)
            <div class="mb-6 p-4 border-4 border-black bg-gray-50">
                <h3 class="pixel-font text-sm mb-3">{{ $payments->first()->student->name }}</h3>
                
                <div class="grid grid-cols-4 gap-2">
                    @for($week = 1; $week <= 4; $week++)
@php
                        $payment = $payments->where('week_number', $week)->first();
                        $isPaid = $payment && $payment->status === 'paid';
                        
                        // Hitung tanggal Rabu untuk minggu ini
                        $now = Carbon::now();
                        $startOfMonth = Carbon::create($now->year, $now->month)->startOfMonth();
                        $firstWednesday = $startOfMonth->copy()->next(Carbon::WEDNESDAY);
                        $weekWednesday = $firstWednesday->copy()->addWeeks($week - 1);
                        $dateLabel = $weekWednesday->locale('id')->isoFormat('D MMM YYYY');
                        
                        $highlightClass = (isset($isWednesday) && $isWednesday && $week == $currentWeek) ? 'ring-4 ring-red-500 bg-yellow-200 animate-pulse shadow-lg border-red-400' : '';
                        @endphp
                        <div class="text-center p-2 {{ $isPaid ? 'bg-green-100 border-green-400' : 'bg-red-100 border-red-400' }} {{ $highlightClass }} border-2 {{ $isPaid ? '' : 'hover:shadow-md transition-all' }}">
                            <div class="pixel-font text-xs font-bold">Minggu {{ $week }}</div>
<div class="pixel-font text-xs text-gray-600">Rabu, {{ $dateLabel }}</div>
                            <div class="pixel-font text-xs font-bold mt-1">
                                @if($isPaid)
                                    <span class="text-green-700">✓ Rp 5.000</span>
                                @else
                                    <span class="text-red-700">✗ Rp 5.000</span>
                                @endif
                            </div>
                            @if(!$isPaid)
                                <button class="pixel-button px-2 py-1 {{ isset($isWednesday) && $isWednesday && $week == $currentWeek ? 'bg-green-500 animate-pulse shadow-lg' : 'bg-blue-400' }} text-black text-xs mt-2 font-bold" 
                                        onclick="showPaymentModal({{ $payment->id ?? '' }}, '{{ $payments->first()->student->name }}', {{ $week }}, {{ $payments->first()->student->id }})">
                                    BAYAR SEKARANG
                                </button>
                            @endif
                        </div>
                    @endfor
                </div>
                
                <!-- Total per siswa -->
                <div class="mt-3 text-right">
                    <span class="pixel-font text-xs">
                        Total: <span class="font-bold">Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}</span>
                        | Lunas: <span class="font-bold text-green-700">{{ $payments->where('status', 'paid')->count() }}/4</span>
                    </span>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Link ke daftar tunggakan -->
    <div class="text-center mt-6">
        <button onclick="showArrearsList()" class="pixel-button px-6 py-3 bg-red-400 text-black pixel-font text-xs">
            🚨 LIHAT DAFTAR TUNGGAKAN
        </button>
        <a href="{{ route('bendahara.dashboard') }}" class="pixel-button px-6 py-3 bg-gray-400 text-black pixel-font text-xs ml-4">
            ← KEMBALI
        </a>
    </div>
</div>

<!-- Modal Daftar Tunggakan -->
<div id="arrearsListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="pixel-card p-6 bg-white max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <h3 class="pixel-font text-lg text-center mb-4">DAFTAR SISWA MENUNGGAK</h3>
        
        <div class="pixel-card p-4 mb-4 bg-blue-200 text-center">
            <div class="pixel-font text-sm font-bold text-blue-700">BULAN MARET 2026</div>
            <div class="pixel-font text-xs text-gray-600">Pembayaran kas setiap hari Rabu: 5, 12, 19, 26 Maret</div>
        </div>
        
        <!-- Total Tunggakan -->
        <div class="pixel-card p-4 mb-4 bg-red-200 text-center">
            <div class="pixel-font text-2xl font-bold text-red-700">
                Rp {{ number_format($unpaidAmount, 0, ',', '.') }}
            </div>
            <div class="pixel-font text-xs">TOTAL TUNGGAKAN BULAN INI</div>
        </div>
        
        <!-- Daftar Siswa Menunggak -->
        <div class="pixel-card p-4">
            @foreach($paymentsByStudent as $studentId => $payments)
                @php
                $unpaidPayments = $payments->where('status', 'unpaid');
                if ($unpaidPayments->count() === 0) {
                    continue;
                }
                
                $totalArrears = $unpaidPayments->sum('amount');
                $unpaidWeeks = $unpaidPayments->pluck('week_number');
                @endphp
                <div class="mb-4 p-4 border-4 border-black bg-red-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="pixel-font text-sm font-bold">{{ $payments->first()->student->name }}</h3>
                            <p class="pixel-font text-xs text-gray-600">
                                Menunggak {{ $unpaidPayments->count() }} minggu:<br>
                                Minggu {{ implode(', ', $unpaidWeeks->toArray()) }} 
@php
                                $now = Carbon::now();
                                $startOfMonth = Carbon::create($now->year, $now->month)->startOfMonth();
                                $firstWednesday = $startOfMonth->copy()->next(Carbon::WEDNESDAY);
                                foreach($unpaidWeeks as $uw) {
                                    $uwWednesday = $firstWednesday->copy()->addWeeks($uw - 1);
                                    echo '(Rabu, ' . $uwWednesday->locale('id')->isoFormat('D MMM') . ') ';
                                }
                                @endphp
                            </p>
                            <p class="pixel-font text-xs text-gray-500 mt-1">
                                @if(in_array(3, $unpaidWeeks->toArray()))
                                    ⚠️ Pembayaran Rabu 19 Maret belum dilunaskan
                                @endif
                                @if(in_array(4, $unpaidWeeks->toArray()))
                                    ⏳ Pembayaran Rabu 26 Maret akan datang
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="pixel-font text-lg font-bold text-red-700">
                                Rp {{ number_format($totalArrears, 0, ',', '.') }}
                            </div>
                            <div class="pixel-font text-xs">Total Tunggakan</div>
                            <button class="pixel-button px-3 py-1 bg-purple-400 text-black text-xs mt-2"
                                    onclick="showArrearsModal({{ $studentId }}, '{{ $payments->first()->student->name }}', {{ $totalArrears }}, '{{ implode(',', $unpaidWeeks->toArray()) }}')">
                                LUNASI TUNGGAKAN
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <button onclick="closeArrearsList()" class="pixel-button px-6 py-3 bg-gray-400 text-black pixel-font text-xs">
                TUTUP
            </button>
        </div>
    </div>
</div>

<!-- Modal Pelunasan Tunggakan -->
<div id="arrearsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="pixel-card p-6 bg-white max-w-md w-full mx-4">
        <h3 class="pixel-font text-lg text-center mb-4">LUNASI TUNGGAKAN</h3>
        
        <form id="arrearsForm" class="space-y-4">
            <input type="hidden" id="arrears_student_id" name="student_id">
            
            <div>
                <label class="pixel-font text-xs">Nama Siswa:</label>
                <div id="arrears_student_name" class="pixel-card p-2 bg-gray-100 text-sm font-bold"></div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Minggu Tunggak:</label>
                <div id="arrears_weeks" class="pixel-card p-2 bg-red-100 text-sm font-bold"></div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Total Tunggakan:</label>
                <div id="arrears_total" class="pixel-card p-2 bg-red-100 text-lg font-bold text-red-700"></div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Tanggal Pelunasan:</label>
                <input type="date" id="arrears_date" name="payment_date" 
                       class="pixel-card px-3 py-2 text-xs w-full" required>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Keterangan:</label>
                <input type="text" id="arrears_description" name="description" 
                       placeholder="Pelunasan tunggakan kas" 
                       class="pixel-card px-3 py-2 text-xs w-full">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="pixel-button px-4 py-2 bg-green-400 text-black pixel-font text-xs flex-1">
                    LUNASI SEKARANG
                </button>
                <button type="button" onclick="closeArrearsModal()" 
                        class="pixel-button px-4 py-2 bg-red-400 text-black pixel-font text-xs flex-1">
                    BATAL
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

<!-- Modal Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="pixel-card p-6 bg-white max-w-md w-full mx-4">
        <h3 class="pixel-font text-lg text-center mb-4">CATAT PEMBAYARAN</h3>
        
        <form id="paymentForm" class="space-y-4">
            <input type="hidden" id="payment_id" name="payment_id">
            
            <div>
                <label class="pixel-font text-xs">Nama Siswa:</label>
                <div id="student_name" class="pixel-card p-2 bg-gray-100 text-sm font-bold"></div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Minggu Ke:</label>
                <div id="week_number" class="pixel-card p-2 bg-gray-100 text-sm font-bold"></div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Jumlah:</label>
                <div class="pixel-card p-2 bg-green-100 text-sm font-bold">Rp 5.000</div>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Tanggal Pembayaran:</label>
                <input type="date" id="payment_date" name="payment_date" 
                       class="pixel-card px-3 py-2 text-xs w-full" required>
            </div>
            
            <div>
                <label class="pixel-font text-xs">Keterangan:</label>
                <input type="text" id="description" name="description" 
                       placeholder="Pembayaran kas mingguan" 
                       class="pixel-card px-3 py-2 text-xs w-full">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="pixel-button px-4 py-2 bg-green-400 text-black pixel-font text-xs flex-1">
                    SIMPAN PEMBAYARAN
                </button>
                <button type="button" onclick="closePaymentModal()" 
                        class="pixel-button px-4 py-2 bg-red-400 text-black pixel-font text-xs flex-1">
                    BATAL
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Simple modal functions
function showPaymentModal(paymentId, studentName, week, studentId) {
    console.log('Opening modal for:', paymentId, studentName, week, studentId);
    
    // Set form values
    document.getElementById('payment_id').value = paymentId;
    document.getElementById('payment_id').dataset.studentId = studentId;
    document.getElementById('student_name').textContent = studentName;
    document.getElementById('week_number').textContent = week;
@php $jsDate = $weekWednesday->toDateString(); $jsDesc = "Pembayaran kas Minggu $week - $dateLabel"; @endphp
    document.getElementById('payment_date').value = '{{ $jsDate }}';
    document.getElementById('description').value = `{{ addslashes($jsDesc) }}`;
    
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
@endsection
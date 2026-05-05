@extends('layouts.app')

@section('title', 'Pembayaran Mingguan - Sederhana')

@section('content')
<div class="dashboard-layout">
    @include('components.bendahara-sidebar')

    <div class="main-area">
        <main class="main-content">
            <!-- Greeting -->
            <section class="greeting-section mb-8">
                <div class="greeting-card">
                    <!-- Month Navigation -->
                    <div class="flex items-center justify-between mb-6">
                        <a href="?month={{ $prevMonth ?? ($month == 1 ? 12 : $month - 1) }}&year={{ $prevYear ?? ($month == 1 ? $year - 1 : $year) }}" class="nav-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Bulan Sebelumnya
                        </a>
                        <div class="text-center">
                            <h1 class="greeting-title">{{ $currentMonthName ?? \Carbon\Carbon::create($year ?? now()->year, $month ?? now()->month)->locale('id')->translatedFormat('F Y') }}</h1>
                            <p class="greeting-subtitle">Pembayaran Mingguan Siswa</p>
                        </div>
                        <a href="?month={{ $nextMonth ?? ($month == 12 ? 1 : $month + 1) }}&year={{ $nextYear ?? ($month == 12 ? $year + 1 : $year) }}" class="nav-btn">
                            Bulan Selanjutnya
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="stats-section mb-8">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon payment">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                            </div>
                            <div class="stat-title">Total Siswa</div>
                        </div>
                        <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                        <div class="stat-description">Siswa aktif bulan ini</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon income">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Sudah Bayar</div>
                        </div>
                        <div class="stat-value">{{ $paidBills ?? 0 }}</div>
                        <div class="stat-description">Dari {{ $totalBills ?? 0 }} total tagihan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 0l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div class="stat-title">Belum Bayar</div>
                        </div>
                        <div class="stat-value">{{ $unpaidBills ?? 0 }}</div>
                        <div class="stat-description">Masih menunggak pembayaran</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon balance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="stat-title">Kas Masuk</div>
                        </div>
                        <div class="stat-value">Rp {{ number_format($paidAmount ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-description">Total pembayaran bulan ini</div>
                    </div>
                </div>
            </section>

            <!-- Payment Table -->
            <section class="tables-section">
                <div class="table-card">
                    <div class="table-header">
                        <h2 class="table-title">Status Pembayaran Mingguan</h2>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Siswa</th>
                                    <th class="text-center">Minggu 1</th>
                                    <th class="text-center">Minggu 2</th>
                                    <th class="text-center">Minggu 3</th>
                                    <th class="text-center">Minggu 4</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $studentIndex = 0;
                                @endphp
                                @forelse($paymentsByStudent ?? [] as $studentId => $payments)
                                @php
                                    $index = ++$studentIndex;
                                    $paidCount = $payments->where('status', 'paid')->count();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="text-center font-semibold">{{ $index }}</td>
                                    <td class="font-semibold">{{ $payments->first()->student->name ?? 'Unknown' }}</td>
                                    @for($week = 1; $week <= 4; $week++)
                                        @php
                                            $payment = $payments->where('week_number', $week)->first();
                                            $status = $payment && $payment->status == 'paid' ? 'success' : 'danger';
                                            $icon = $status == 'success' ? '✓' : '✗';
                                        @endphp
                                        <td class="text-center">
                                            <span class="status-badge {{ $status }}">{{ $icon }}</span>
                                        </td>
                                    @endfor
                                    <td class="text-center font-bold">
                                        <span class="status-badge {{ $paidCount == 4 ? 'success' : 'danger' }}">
                                            {{ $paidCount }}/4
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($paidCount < 4)
                                            <button onclick="showPaymentModal({{ $payments->first()->student->id }}, '{{ $payments->first()->student->name }}', {{ $month ?? now()->month }}, {{ $year ?? now()->year }})" class="action-btn">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Bayar
                                            </button>
                                        @else
                                            <span class="text-green-600 text-sm">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500">
                                        Belum ada data pembayaran untuk bulan {{ $currentMonthName ?? \Carbon\Carbon::create($year ?? now()->year, $month ?? now()->month)->locale('id')->translatedFormat('F Y') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Dashboard CSS -->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
/* All dashboard CSS copied from dashboard.blade.php - sidebar, main-area, cards, tables, responsive */
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

<!-- Modal Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">Proses Pembayaran Mingguan</h3>
        
        <form id="paymentForm" class="space-y-4">
            <input type="hidden" id="student_id" name="student_id">
            <input type="hidden" id="payment_month" name="month">
            <input type="hidden" id="payment_year" name="year">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa:</label>
                <div id="student_name" class="p-2 bg-gray-100 rounded font-semibold"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan:</label>
                <div id="payment_month_display" class="p-2 bg-gray-100 rounded"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Minggu:</label>
                <select id="week_select" name="week_number" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Pilih Minggu --</option>
                    <option value="1">Minggu 1</option>
                    <option value="2">Minggu 2</option>
                    <option value="3">Minggu 3</option>
                    <option value="4">Minggu 4</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah:</label>
                <div class="p-2 bg-green-100 rounded font-semibold">Rp {{ number_format($weeklyPaymentAmount, 0, ',', '.') }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran:</label>
                <input type="date" id="payment_date" name="payment_date" 
                       class="w-full p-2 border border-gray-300 rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan:</label>
                <input type="text" id="payment_description" name="description" 
                       placeholder="Pembayaran kas mingguan" 
                       class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 font-semibold">
                    Proses Pembayaran
                </button>
                <button type="button" onclick="closePaymentModal()" 
                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showPaymentModal(studentId, studentName, month, year) {
    document.getElementById('student_id').value = studentId;
    document.getElementById('student_name').textContent = studentName;
    document.getElementById('payment_month').value = month;
    document.getElementById('payment_year').value = year;
    
    // Format month name
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('payment_month_display').textContent = monthNames[month - 1] + ' ' + year;
    
    // Set default date
    document.getElementById('payment_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('payment_description').value = `Pembayaran kas ${monthNames[month - 1]} - ${studentName}`;
    
    // Show modal
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
}

// Handle form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Memproses...';
    submitBtn.disabled = true;
    
    // Create transaction first
    fetch('/bendahara/kas/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.transaction) {
            // Find and process the weekly payment
            return fetch('/bendahara/api/find-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    student_id: formData.get('student_id'),
                    week_number: formData.get('week_number'),
                    month: formData.get('month'),
                    year: formData.get('year'),
                    transaction_id: data.transaction.id
                })
            });
        } else {
            throw new Error(data.message || 'Transaksi gagal');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.payment) {
            // Process the payment
            return fetch('/bendahara/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_id: data.payment.id,
                    transaction_id: data.transaction_id
                })
            });
        } else {
            throw new Error('Pembayaran tidak ditemukan');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Pembayaran berhasil dicatat!');
            closePaymentModal();
            location.reload();
        } else {
            showErrorToast('Gagal memproses pembayaran: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
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
</script>
@endsection


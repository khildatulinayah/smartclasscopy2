@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    @include('components.bendahara-sidebar')

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Manajemen Kas Digital</h1>
                    <p class="greeting-subtitle">{{ \Carbon\Carbon::now()->locale('id')->format('F Y') }}</p>
                </div>
            </section>

            <section class="stats-section mb-8">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon income">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <div class="stat-title">Kas Masuk</div>
                        </div>
                        <div class="stat-value text-green-600" id="total-income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                        <div class="stat-description">Total pemasukan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon expense">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </div>
                            <div class="stat-title">Kas Keluar</div>
                        </div>
                        <div class="stat-value text-red-600" id="total-expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                        <div class="stat-description">Total pengeluaran</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon balance">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="stat-title">Saldo Akhir</div>
                        </div>
                        <div class="stat-value text-blue-600" id="balance">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                        <div class="stat-description">Saldo kas tersedia</div>
                    </div>
                </div>
            </section>

            <section class="mb-8">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex gap-4">
                        <a href="{{ route('bendahara.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m14 14l-7-7m-7 7l7-7m-7 7l7-7m7 7l7-7m-7 7l7-7m-7 7l7-7m-7 7l7-7"></path></svg>
                            Dashboard
                        </a>
                        <button onclick="openTransactionModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg font-semibold transition flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Transaksi Baru
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="filterTransactions('all')" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-semibold transition-all ring-0 hover:ring-2 hover:ring-blue-400">
                            Semua
                        </button>
                        <button onclick="filterTransactions('income')" class="px-5 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded-xl font-semibold transition-all ring-0 hover:ring-2 hover:ring-emerald-400">
                            Masuk
                        </button>
                        <button onclick="filterTransactions('expense')" class="px-5 py-3 bg-red-100 hover:bg-red-200 text-red-800 rounded-xl font-semibold transition-all ring-0 hover:ring-2 hover:ring-red-400">
                            Keluar
                        </button>
                    </div>
                </div>
            </section>

            <section class="tables-section">
                <div class="table-card flex-1">
                    <div class="table-header flex justify-between items-center">
                        <h2 class="table-title">Riwayat Transaksi</h2>
                        <button onclick="exportCSV()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10l-5.5 5.5m0 0L8 19l5.5-5.5m0 0L19 8m-5.5 5.5v11m0 0l-5.5-5.5m5.5 5.5L19 12"></path>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                    <div class="relative p-6 pb-4">
                        <input type="text" id="transaction-search" placeholder="🔍 Cari transaksi, siswa, atau keterangan..." 
                               class="w-full pl-12 pr-6 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-lg placeholder-gray-500">
                        <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div id="transactions-container" class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        <!-- Dynamic content -->
                    </div>
                    <div id="empty-state" class="text-center py-16 hidden bg-gradient-to-b from-gray-50">
                        <div class="text-6xl mb-6">💰</div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">Belum ada transaksi kas</h3>
                        <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">Tambahkan transaksi pertama Anda untuk memulai pengelolaan keuangan kelas</p>
                        <button onclick="openTransactionModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-bold text-lg shadow-lg transform hover:-translate-y-1 transition-all">
                            + Transaksi Pertama
                        </button>
                    </div>
                    <div id="loading" class="text-center py-12 hidden">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-full bg-indigo-100 text-indigo-700 animate-pulse">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"></circle>
                                <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat transaksi...
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transaction-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Transaksi</h3>
                <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="transaction-form" onsubmit="saveTransaction(event)">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative">
                            <input type="radio" name="type" value="income" class="peer sr-only" checked>
                            <div class="border-2 border-gray-200 rounded-lg p-3 cursor-pointer text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                                <div class="text-green-600 font-medium">Kas Masuk</div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="expense" class="peer sr-only">
                            <div class="border-2 border-gray-200 rounded-lg p-3 cursor-pointer text-center peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                                <div class="text-red-600 font-medium">Kas Keluar</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp)</label>
                    <input type="number" id="amount" name="amount" required min="1" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                </div>
                
                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa (Opsional)</label>
                    <select id="student_id" name="student_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <input type="text" id="description" name="description" required maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Kas mingguan">
                </div>
                
                <div class="mb-6">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" id="date" name="date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeTransactionModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg font-medium transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div id="success-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full transition-transform duration-300 z-50">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span id="toast-message">Transaksi berhasil disimpan!</span>
    </div>
</div>

<script>
let transactions = [];
let currentFilter = 'all';
let searchTerm = '';

document.addEventListener('DOMContentLoaded', () => {
    loadTransactions();
    document.getElementById('transaction-search').addEventListener('input', e => {
        searchTerm = e.target.value.toLowerCase();
        renderTransactions();
    });
});

async function loadTransactions() {
    showLoading();
    try {
        const res = await fetch(`{{ route('bendahara.api.transactions') }}?t=${Date.now()}`);
        const data = await res.json();
        transactions = data.transactions;
        updateSummary(data.summary);
        renderTransactions();
    } catch (e) {
        console.error(e);
    } finally {
        hideLoading();
    }
}

function renderTransactions() {
    const container = document.getElementById('transactions-container');
    const empty = document.getElementById('empty-state');
    
    let filtered = transactions.filter(t => {
        const matchFilter = currentFilter === 'all' || t.type === currentFilter;
        const matchSearch = !searchTerm || t.description.toLowerCase().includes(searchTerm) || 
            (t.student?.name?.toLowerCase()?.includes(searchTerm)) ||
            t.date.includes(searchTerm);
        return matchFilter && matchSearch;
    });
    
    if (!filtered.length) {
        container.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    
    empty.classList.add('hidden');
    container.innerHTML = filtered.map(createTransactionCard).join('');
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.onclick = () => deleteTransaction(btn.dataset.id);
    });
}

function createTransactionCard(t) {
    const isIncome = t.type === 'income';
    return `
        <div class="p-6 hover:bg-gray-50/50 transition-colors border-b border-gray-100 last:border-b-0">
            <div class="flex items-start lg:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="px-3 py-1.5 rounded-full text-xs font-bold bg-opacity-10 border ${isIncome ? 'bg-green text-green-800 border-green-200' : 'bg-red text-red-800 border-red-200'}">
                            ${isIncome ? 'MASUK' : 'KELUAR'}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 leading-tight">${t.description}</h3>
                            <div class="flex items-center gap-4 text-sm text-gray-500 mt-1 flex-wrap">
                                ${t.student ? `<span class="font-medium">${t.student.name}</span>` : ''} 
                                <span>${new Date(t.date).toLocaleDateString('id-ID')}</span>
                                <span>${t.creator?.name || 'Sistem'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-2xl font-bold ${isIncome ? 'text-green-600' : 'text-red-600'} leading-tight">
                        ${isIncome ? '+' : '-'} Rp ${Number(t.amount).toLocaleString('id-ID')}
                    </div>
                    <button class="delete-btn mt-1 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-sm font-semibold transition-all ml-auto border border-red-200 invisible hover-visible opacity-0 group-hover:opacity-100 group-hover:translate-y-0">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    `;
}

function updateSummary(s) {
    ['total-income', 'total-expense', 'balance'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = `Rp ${Number(s[id.replace('-', '')]).toLocaleString('id-ID')}`;
    });
}

function filterTransactions(type) {
    currentFilter = type;
    document.querySelectorAll('.filter-btn, button[onclick*="filterTransactions"]').forEach(btn => btn.classList.remove('ring-2'));
    (event.target.matches('button') ? event.target : event.target.closest('button')).classList.add('ring-2', 'ring-indigo-500');
    renderTransactions();
}

function showLoading() { 
    document.getElementById('loading').classList.remove('hidden'); 
    document.getElementById('transactions-container').style.display = 'none'; 
}
function hideLoading() { 
    document.getElementById('loading').classList.add('hidden'); 
    document.getElementById('transactions-container').style.display = 'block'; 
}

window.openTransactionModal = () => document.getElementById('transaction-modal').classList.remove('hidden');
window.closeTransactionModal = () => document.getElementById('transaction-modal').classList.add('hidden') || document.getElementById('transaction-form').reset();

window.saveTransaction = async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    try {
        const res = await fetch('{{ route("bendahara.kas.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            closeTransactionModal();
            setTimeout(loadTransactions, 300);
            showToast('✅ Transaksi tersimpan!');
        } else showToast('❌ ' + (result.message || 'Gagal simpan'), true);
    } catch {
        showToast('❌ Koneksi gagal', true);
    }
};

window.deleteTransaction = async (id) => {
    if (!confirm('Hapus transaksi permanen?')) return;
    try {
        const res = await fetch(`/bendahara/transactions/${id}`, { 
            method: 'DELETE', 
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
        });
        const result = await res.json();
        if (result.success) {
            loadTransactions();
            showToast('🗑️ Transaksi dihapus');
        } else showToast('❌ Gagal hapus', true);
    } catch {
        showToast('❌ Error', true);
    }
};

window.exportCSV = () => {
    const csv = ['Deskripsi,Jenis,Rp,Siswa,Tanggal,Pembuat\n'] + 
        transactions.map(t => `"${t.description}","${t.type}","${t.amount}","${t.student?.name||''}","${t.date}","${t.creator?.name||'Sistem'}"`).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `kas-${new Date().toISOString().slice(0,10)}.csv`;
    link.click();
};

window.showToast = (msg, isError) => {
    const toast = document.getElementById('success-toast');
    document.getElementById('toast-message').textContent = msg;
    toast.style.background = isError ? '#ef4444' : '#10b981';
    toast.classList.remove('translate-y-full');
    setTimeout(() => toast.classList.add('translate-y-full'), 3500);
};

document.getElementById('transaction-modal')?.addEventListener('click', e => e.target.id === 'transaction-modal' && closeTransactionModal());
</script>

{{-- Dashboard CSS --}}
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-layout { display: flex; height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); font-family: 'Inter', sans-serif; }
.sidebar { width: 280px; background: #ffffff; border-right: 1px solid #e5e7eb; box-shadow: 4px 0 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; }
.sidebar-header { padding: 2rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.logo { display: flex; align-items: center; gap: 0.75rem; }
.logo-img { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; object-fit: cover; }
.logo-text { font-size: 1.25rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6, #1d4ed8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-nav { flex: 1; padding: 1rem 0; }
.nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.25rem; color: #6b7280; text-decoration: none; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 0 1rem 1rem 0; margin: 0 0.75rem; position: relative; overflow: hidden; }
.nav-item::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #3b82f6; opacity: 0; transition: opacity 0.25s; }
.nav-item:hover { background: #f8fafc; color: #3b82f6; transform: translateX(2px); }
.nav-item.active { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #1d4ed8; font-weight: 600; }
.nav-item.active::before { opacity: 1; }
.nav-icon { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
.sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid #f3f4f6; }
.user-profile-mini { display: flex; align-items: center; gap: 0.625rem; margin-bottom: 0.75rem; padding: 0.75rem; border-radius: 0.75rem; background: linear-gradient(135deg, #f8fafc, #f1f5f9); }
.user-avatar-mini { width: 2rem; height: 2rem; border-radius: 0.375rem; object-fit: cover; }
.user-name-mini { font-size: 0.8125rem; font-weight: 600; color: #1f2937; }
.user-role-mini { font-size: 0.6875rem; color: #6b7280; font-weight: 500; }
.logout-form { display: block; }
.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; border: none; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: all 0.25s; border: 1px solid #fecaca; }
.logout-btn:hover { background: linear-gradient(135deg, #fecaca, #fda4a4); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
.logout-icon { width: 1rem; height: 1rem; }
.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.main-content { flex: 1; padding: 2rem; overflow-y: auto; scroll-behavior: smooth; }
.greeting-section { margin-bottom: 2rem; }
.greeting-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 2.5rem; border-radius: 1.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; backdrop-filter: blur(10px); }
.greeting-title { font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #1f2937, #374151); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 0.5rem; line-height: 1.1; }
.greeting-subtitle { font-size: 1.125rem; color: #6b7280; font-weight: 400; }
.stats-section { margin-bottom: 2rem; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr)); gap: 1.5rem; }
.stat-card { background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
.stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--tw-gradient-stops)); opacity: 0; transition: opacity 0.3s; }
.stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
.stat-card:hover::before { opacity: 1; }
.stat-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
.stat-icon { width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-icon svg { width: 1.5rem; height: 1.5rem; }
.stat-icon.income { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; }
.stat-icon.expense { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
.stat-icon.balance { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.stat-title { font-size: 0.9375rem; font-weight: 600; color: #374151; }
.stat-value { font-size: 2.25rem; font-weight: 800; line-height: 1; margin-bottom: 0.25rem; }
.stat-description { font-size: 0.875rem; color: #6b7280; font-weight: 500; }
.table-card { background: white; border-radius: 1.25rem; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; overflow: hidden; }
.table-header { padding: 1.75rem 1.75rem 0; display: flex; justify-content: space-between; align-items: center; }
.table-title { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
.table-container { padding: 1.25rem 1.75rem 1.75rem; }
#transactions-container { max-height: 24rem; overflow-y: auto; }
#transactions-container::-webkit-scrollbar { width: 6px; }
#transactions-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
#transactions-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
#transactions-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.filter-btn { font-weight: 600; border: 2px solid transparent; }
.filter-btn:hover { transform: translateY(-1px); }
.feature-btn { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; font-weight: 600; border-radius: 0.75rem; padding: 0.875rem 1.5rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4); display: inline-flex; align-items: center; gap: 0.5rem; }
.feature-btn:hover { background: linear-gradient(135deg, #2563eb, #1e40af); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5); }
@media (max-width: 1024px) { .stats-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .sidebar { transform: translateX(-100%); position: fixed; z-index: 40; height: 100vh; transition: transform 0.3s; } .main-content { padding: 1.5rem; } .stats-grid { gap: 1rem; } .table-header { flex-direction: column; gap: 1rem; align-items: stretch; } }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.stat-card, .greeting-card { animation: fadeIn 0.6s ease-out; }
</style>
@endsection

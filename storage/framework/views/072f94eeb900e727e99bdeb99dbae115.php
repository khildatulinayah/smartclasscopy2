<!-- Detail Modal Income (Kas Masuk) -->
<div id="incomeDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between gap-4 sticky top-0 bg-white">
            <div>
<h2 class="text-2xl font-bold text-gray-900">Detail Kas Masuk</h2>
                <p id="incomeDetailSubtitle" class="text-sm text-gray-500 mt-1">Pilih sumber kas masuk</p>
            </div>
            <button type="button" onclick="closeIncomeDetail()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <div id="incomeDetailLoading" class="text-center py-10 hidden">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-full bg-indigo-100 text-indigo-700 animate-pulse">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"></circle>
                        <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memuat data...
                </div>
            </div>

            <div class="mb-4">
                <div class="flex flex-wrap gap-3 items-center justify-between">
                    <div class="flex-1 min-w-[220px]">
                        <input type="text" id="incomeDetailSearch" placeholder="🔍 Cari pemasukan..." class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-base placeholder-gray-500">
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold" id="incomeDetailCount">0</span> transaksi
                    </div>
                </div>
            </div>

            
            <div class="flex flex-wrap gap-3 mb-4 items-center">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe Kas</label>
                    <select id="incomeDetailType" class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-base bg-white">
                        <option value="all">Semua</option>
                        <option value="weekly">Weekly Payments</option>
                        <option value="non_weekly">Di Luar Weekly</option>
                        <option value="adjustment">Adjustment (Adj.)</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
                    <select id="incomeDetailMonth" class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-base bg-white">
                        <option value="all">Semua</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun</label>
                    <select id="incomeDetailYear" class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-base bg-white">
                        <option value="all">Semua</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="button" id="incomeDetailResetFilters" class="px-4 py-3 rounded-xl font-semibold text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 transition w-full min-w-[140px]">
                        Reset
                    </button>
                </div>
            </div>



            <div id="incomeDetailContainer" class="divide-y divide-gray-100">
                <!-- dynamic -->
            </div>

            <div id="incomeDetailEmpty" class="text-center py-10 hidden">
                <div class="text-6xl mb-3">💰</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada kas masuk</h3>
                <p class="text-gray-500">Transaksi pemasukan akan muncul di sini.</p>
            </div>
        </div>
    </div>
</div>

<script>
// income detail uses the same `transactions` array from simple-cash page.
window.__incomeDetailSource = 'weekly';

window.openIncomeDetail = () => {


    const modal = document.getElementById('incomeDetailModal');
    const container = document.getElementById('incomeDetailContainer');
    const empty = document.getElementById('incomeDetailEmpty');

    document.getElementById('incomeDetailLoading').classList.remove('hidden');

    const searchInput = document.getElementById('incomeDetailSearch');
    if (searchInput) searchInput.value = '';

    // Ensure `transactions` are available (loaded async) before filtering.
    const source = Array.isArray(window.transactions) ? window.transactions : [];

    // debug (bisa dihapus setelah yakin)
    console.log('[IncomeDetail] transactions array:', window.transactions);

    window.__incomeDetailSearchTerm = '';

    // Identify income by common possibilities in backend/API.
    const incomeTransactions = source.filter(t => {
        const type = (t.type ?? '').toString().toLowerCase();
        const jenis = (t.jenis ?? '').toString().toLowerCase();

        return type === 'income' || type === 'kas masuk' || type === 'kas_masuk' ||
               jenis === 'income' || jenis === 'kas masuk' || jenis === 'kas_masuk' ||
               t.type === 'Kas Masuk' || t.jenis === 'Kas Masuk';
    });

    // Split by weekly payment usage.
    // Berdasarkan backend: controller membuat `used_in_weekly_payment`.
    window.__incomeDetailTransactions = incomeTransactions;

    // Render awal pakai applyFilters (berdasarkan dropdown bulan/tahun/tipe kas)

    const filtered = window.__incomeDetailTransactions;
    document.getElementById('incomeDetailCount').textContent = filtered.length;
    container.innerHTML = filtered.map(t => createIncomeDetailCard(t)).join('');
    empty.classList.toggle('hidden', filtered.length !== 0);


    document.getElementById('incomeDetailLoading').classList.add('hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

    window.openIncomeDetailSource = (source) => {
    window.__incomeDetailSource = source;
    window.openIncomeDetail();
};

// Backward compatibility: dulu pakai button weekly/non_weekly.
// Sekarang UI diganti dropdown filter.
window.openIncomeDetailSource = window.openIncomeDetailSource;


window.closeIncomeDetail = () => {
    const modal = document.getElementById('incomeDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

// Ensure modal works even if simple-cash page doesn't set `window.transactions` yet.
window.openIncomeDetail = window.openIncomeDetail || (() => {
    const modal = document.getElementById('incomeDetailModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
});

// Kata kunci fallback (HANYA dipakai kalau backend belum kirim `is_adjustment`,
// mis. API lama yang belum di-deploy). Sengaja TIDAK mengandung kata
// "pelunasan"/"tunggakan" karena dua kata itu juga dipakai transaksi pelunasan
// tunggakan mingguan biasa (processArrears()) yang BUKAN adjustment — kalau
// dimasukkan, dua jenis transaksi itu jadi ketuker lagi.
const ADJUSTMENT_FALLBACK_KEYWORDS = [
    'adjustment', 'adj\\.', 'penyesuaian',
    'kekurangan kas', 'kelebihan kas', 'refund'
];
const ADJUSTMENT_FALLBACK_REGEX = new RegExp('\\b(' + ADJUSTMENT_FALLBACK_KEYWORDS.join('|') + ')\\b', 'i');

window.isAdjustmentTransaction = function (t) {
    // Sumber kebenaran: flag `is_adjustment` dari backend (BendaharaController::getTransactions()),
    // yang dihitung dari FK transactions.payment_adjustment_id — diisi hanya saat
    // adjustment (kekurangan/refund) benar-benar diproses lewat processShortage()/processRefund().
    if (typeof t.is_adjustment !== 'undefined' && t.is_adjustment !== null) {
        return !!t.is_adjustment;
    }
    // Fallback kalau field belum ada di response (mis. belum sempat deploy ulang API).
    return ADJUSTMENT_FALLBACK_REGEX.test((t.description || '').toString());
};

window.isWeeklyPaymentTransaction = function (t) {
    return !!t.used_in_weekly_payment;
};

// Satu-satunya sumber kebenaran untuk kategori "Tipe Kas".
// Dipakai BAIK oleh filter dropdown (applyFilters) MAUPUN oleh kartu transaksi
// (createIncomeDetailCard), supaya badge yang tampil di kartu selalu konsisten
// dengan kategori yang dipakai untuk memfilter — tidak ada lagi celah di mana
// satu transaksi kehitung masuk ke lebih dari satu kategori.
// Urutan prioritas: adjustment dicek duluan, jadi transaksi koreksi/pelunasan
// tidak akan pernah nyasar ke kategori "weekly" atau "non_weekly".
window.classifyIncomeType = function (t) {
    if (window.isAdjustmentTransaction(t)) return 'adjustment';
    return window.isWeeklyPaymentTransaction(t) ? 'weekly' : 'non_weekly';
};

function createIncomeDetailCard(t) {
    const studentName = t.student?.name || (t.used_in_weekly_payment ? 'Siswa' : '');
    const showStudent = !!t.student?.name;
    const formattedDate = t.date ? new Date(t.date).toLocaleDateString('id-ID') : '-';

    // Label periode kas: untuk transaksi yang terhubung weekly payment, tampilkan
    // periode berdasarkan weekly_payments.month/year (bukan tanggal transaksi),
    // supaya konsisten dengan dasar pengelompokan/filter bulan-tahun.
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const periodLabel = (t.used_in_weekly_payment && t.weekly_payment_month && t.weekly_payment_year)
        ? `${monthNames[t.weekly_payment_month - 1]} ${t.weekly_payment_year}`
        : null;

    const incomeType = window.classifyIncomeType(t);
    const typeBadge = incomeType === 'adjustment'
        ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">ADJUSTMENT</span>`
        : (incomeType === 'weekly'
            ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">WEEKLY</span>`
            : `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200">DI LUAR WEEKLY</span>`);

    return `
        <div class="p-4 hover:bg-gray-50/50 transition-colors">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                            PEMASUKAN
                        </span>
                        ${typeBadge}
                        <span class="text-sm font-semibold text-gray-900">${escapeHtml(t.description || '')}</span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 flex gap-3 flex-wrap">
                        ${showStudent ? `<span>👤 ${escapeHtml(studentName)}</span>` : ''}
                        <span>📅 ${escapeHtml(formattedDate)}</span>
                        ${periodLabel ? `<span>🗓️ Periode kas: ${escapeHtml(periodLabel)}</span>` : ''}
                        <span>👁️ ${escapeHtml(t.creator?.name || 'Sistem')}</span>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-xl font-bold text-green-600">+ Rp ${Number(t.amount).toLocaleString('id-ID')}</div>
                    <div class="mt-2">
                        <button class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-semibold transition border border-blue-600" onclick="window.showTransactionDetail(${t.id})">
                            Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(str) {
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '<')
        .replaceAll('>', '>')
        .replaceAll('"', '"')
        .replaceAll("'", '&#039;');
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('incomeDetailSearch');
    if (!searchInput) return;

    function getSelectedMonthYear() {
        const monthEl = document.getElementById('incomeDetailMonth');
        const yearEl = document.getElementById('incomeDetailYear');
        const monthVal = monthEl?.value ?? 'all';
        const yearVal = yearEl?.value ?? 'all';
        return { month: monthVal, year: yearVal };
    }

    function applyFilters() {
        const container = document.getElementById('incomeDetailContainer');
        const empty = document.getElementById('incomeDetailEmpty');
        const list = window.__incomeDetailTransactions || [];

        const term = (window.__incomeDetailSearchTerm || '').toLowerCase();
        const { month, year } = getSelectedMonthYear();
        const typeEl = document.getElementById('incomeDetailType');
        const selectedType = typeEl?.value ?? 'all';

        function matchesType(t) {
            if (selectedType === 'all') return true;
            return window.classifyIncomeType(t) === selectedType;
        }

        const filtered = list.filter(t => {
            const desc = (t.description || '').toLowerCase();
            const student = (t.student?.name || '').toLowerCase();
            const dateStr = (t.date || '').toLowerCase();

            const matchesSearch = !term || desc.includes(term) || student.includes(term) || dateStr.includes(term);

            let matchesMonth = true;
            let matchesYear = true;

            // Sumber periode:
            // - Transaksi yang terhubung weekly payment (used_in_weekly_payment) -> pakai
            //   weekly_payment_month/year (dari relasi weekly_payments.month/year), BUKAN tanggal transaksi.
            // - Transaksi manual (tidak terhubung weekly payment) -> tetap pakai tanggal transaksi (t.date).
            if (window.isWeeklyPaymentTransaction(t)) {
                const wMonth = t.weekly_payment_month;
                const wYear = t.weekly_payment_year;

                if (month !== 'all') matchesMonth = wMonth != null && wMonth.toString() === month;
                if (year !== 'all') matchesYear = wYear != null && wYear.toString() === year;
            } else if (t.date) {
                const d = new Date(t.date);
                if (month !== 'all') matchesMonth = (d.getMonth() + 1).toString() === month;
                if (year !== 'all') matchesYear = d.getFullYear().toString() === year;
            } else {
                if (month !== 'all') matchesMonth = false;
                if (year !== 'all') matchesYear = false;
            }

            return matchesSearch && matchesMonth && matchesYear && matchesType(t);
        });

        // DEBUG biar gampang lihat kenapa kosong
        // console.log('[IncomeDetail][applyFilters]', { term, month, year, selectedType, total: list.length, filtered: filtered.length });


        document.getElementById('incomeDetailCount').textContent = filtered.length;

        container.innerHTML = filtered.map(t => createIncomeDetailCard(t)).join('');
        empty.classList.toggle('hidden', filtered.length !== 0);
    }

    searchInput.addEventListener('input', (e) => {
        window.__incomeDetailSearchTerm = e.target.value.toLowerCase();
        applyFilters();
    });

    document.getElementById('incomeDetailMonth')?.addEventListener('change', () => {
        applyFilters();
    });

    document.getElementById('incomeDetailYear')?.addEventListener('change', () => {
        applyFilters();
    });

    document.getElementById('incomeDetailResetFilters')?.addEventListener('click', () => {
        const monthEl = document.getElementById('incomeDetailMonth');
        const yearEl = document.getElementById('incomeDetailYear');
        if (monthEl) monthEl.value = 'all';
        if (yearEl) yearEl.value = 'all';
        window.__incomeDetailSearchTerm = '';
        if (searchInput) searchInput.value = '';
        applyFilters();
    });

    // initial render when modal content already prepared
    applyFilters();

    const modal = document.getElementById('incomeDetailModal');

    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeIncomeDetail();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modalEl = document.getElementById('incomeDetailModal');
            if (modalEl && !modalEl.classList.contains('hidden')) closeIncomeDetail();
        }
    });
});
</script><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/detail.blade.php ENDPATH**/ ?>
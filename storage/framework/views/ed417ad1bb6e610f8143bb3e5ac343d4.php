<!-- Modal Selisih Nominal Pembayaran -->
<div id="differenceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Lunasi Selisih Nominal</h3>
            <p class="text-sm text-gray-600 mt-1">Nominal kas telah berubah setelah pembayaran dilakukan</p>
        </div>
        
        <form id="differenceForm" class="p-6 space-y-4">
            <input type="hidden" id="diff_payment_id" name="payment_id">
            <input type="hidden" id="diff_old_nominal" name="old_nominal">
            <input type="hidden" id="diff_new_nominal" name="new_nominal">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Siswa:</label>
                <div id="diff_student_name" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minggu Ke:</label>
                <div id="diff_week_number" class="px-3 py-2 bg-gray-100 rounded-lg text-sm font-semibold"></div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nominal Lama:</label>
                    <div id="diff_old_amount" class="px-3 py-2 bg-orange-50 rounded-lg text-sm font-semibold text-orange-700"></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nominal Baru:</label>
                    <div id="diff_new_amount" class="px-3 py-2 bg-green-50 rounded-lg text-sm font-semibold text-green-700"></div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="text-xs text-blue-600 font-semibold mb-1">Selisih yang harus dibayarkan:</div>
                <div id="diff_amount" class="text-2xl font-bold text-blue-700"></div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pelunasan:</label>
                <input type="date" id="diff_date" name="payment_date" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan:</label>
                <input type="text" id="diff_description" name="description" 
                       placeholder="PELUNASAN SELISIH NOMINAL KAS" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full">
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Lunasi Selisih
                </button>
                <button type="button" onclick="closeDifferenceModal()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar Selisih Nominal Pending -->
<div id="paymentDifferencesListModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-800">Selisih Nominal Menunggu Penyelesaian</h3>
            <p class="text-sm text-gray-600 mt-1">Daftar pembayaran yang memerlukan pelunasan atau pengembalian dana karena perubahan nominal kas</p>
        </div>
        
        <div class="p-6">
            <div id="differencesSummary" class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <div class="text-sm font-semibold text-yellow-800">Total Pending</div>
                    <div id="totalPending" class="text-2xl font-bold text-yellow-700">0</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-sm font-semibold text-blue-800">Pelunasan Dibutuhkan</div>
                    <div id="totalSettlement" class="text-2xl font-bold text-blue-700">Rp 0</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                    <div class="text-sm font-semibold text-orange-800">Pengembalian Dana</div>
                    <div id="totalRefund" class="text-2xl font-bold text-orange-700">Rp 0</div>
                </div>
            </div>
            
            <div id="differencesList" class="space-y-4">
                <div class="text-center py-8 text-gray-500">
                    <p>Memuat data selisih nominal...</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 sticky bottom-0 bg-white">
            <button onclick="closePaymentDifferencesListModal()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// ========== PAYMENT DIFFERENCE HANDLERS ==========

function showDifferenceModal(paymentId, difference, studentName, week) {
    console.log('Opening difference modal:', { paymentId, difference, studentName, week });
    
    const modal = document.getElementById('differenceModal');
    if (!modal) {
        showErrorToast('Modal tidak ditemukan!');
        return;
    }
    
    // Set form values
    document.getElementById('diff_payment_id').value = paymentId;
    document.getElementById('diff_student_name').textContent = studentName;
    document.getElementById('diff_week_number').textContent = `Minggu ${week}`;
    document.getElementById('diff_old_amount').textContent = `Rp <?php echo e(number_format($weeklyPaymentAmount - $weeklyPaymentAmount, 0, ',', '.')); ?>`;
    document.getElementById('diff_new_amount').textContent = `Rp <?php echo e(number_format($weeklyPaymentAmount, 0, ',', '.')); ?>`;
    document.getElementById('diff_amount').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(difference)}`;
    
    // Set current date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('diff_date').value = today;
    
    // Set description
    const monthNames = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 
                       'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
    const urlParams = new URLSearchParams(window.location.search);
    const currentMonth = parseInt(urlParams.get('month')) || new Date().getMonth() + 1;
    const currentYear = parseInt(urlParams.get('year')) || new Date().getFullYear();
    const monthName = monthNames[currentMonth - 1];
    
    document.getElementById('diff_description').value = `PELUNASAN SELISIH NOMINAL MINGGU ${week} ${monthName} ${currentYear} - ${studentName}`;
    
    // Show modal
    modal.classList.remove('hidden');
}

function closeDifferenceModal() {
    const modal = document.getElementById('differenceModal');
    if (modal) {
        modal.classList.add('hidden');
        document.getElementById('differenceForm').reset();
    }
}

function showPaymentDifferencesList() {
    const modal = document.getElementById('paymentDifferencesListModal');
    if (!modal) {
        showErrorToast('Modal tidak ditemukan!');
        return;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Fetch payment differences
    loadPaymentDifferences();
}

function closePaymentDifferencesListModal() {
    const modal = document.getElementById('paymentDifferencesListModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function loadPaymentDifferences() {
    fetch('/bendahara/api/payment-differences?status=pending')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPaymentDifferences(data.data);
                updateDifferencesSummary(data.data);
            } else {
                showErrorToast('Gagal memuat data selisih nominal');
            }
        })
        .catch(error => {
            console.error('Error loading payment differences:', error);
            showErrorToast('Gagal memuat data selisih nominal');
        });
}

function displayPaymentDifferences(differences) {
    const listContainer = document.getElementById('differencesList');
    
    if (differences.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <p>Tidak ada selisih nominal yang menunggu penyelesaian</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    differences.forEach((diff, index) => {
        const isSettlement = diff.action_type === 'settlement';
        const bgColor = isSettlement ? 'blue-50' : 'orange-50';
        const borderColor = isSettlement ? 'blue-200' : 'orange-200';
        const textColor = isSettlement ? 'text-blue-700' : 'text-orange-700';
        const actionText = isSettlement ? 'Pelunasan' : 'Pengembalian Dana';
        const actionButton = isSettlement ? 'Settlement' : 'Refund';
        
        html += `
            <div class="bg-${bgColor} border border-${borderColor} rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">${diff.student.name}</h4>
                        <p class="text-sm text-gray-600 mt-1">Minggu ${diff.weekly_payment.week_number}</p>
                        <p class="text-xs text-gray-500 mt-1">Dibuat: ${new Date(diff.created_at).toLocaleDateString('id-ID')}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold ${textColor}">
                            Rp ${new Intl.NumberFormat('id-ID').format(diff.difference)}
                        </div>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">
                            ${actionText}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                    <div>
                        <span class="text-gray-600">Nominal Lama:</span>
                        <div class="font-semibold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(diff.old_nominal)}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Nominal Baru:</span>
                        <div class="font-semibold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(diff.new_nominal)}</div>
                    </div>
                </div>
                
                <p class="text-sm text-gray-700 mb-3 bg-white bg-opacity-50 p-2 rounded">
                    ${diff.notes}
                </p>
                
                <button onclick="processPaymentDifference(${diff.id}, '${diff.action_type}')" 
                        class="w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Proses ${actionButton}
                </button>
            </div>
        `;
    });
    
    listContainer.innerHTML = html;
}

function updateDifferencesSummary(differences) {
    const settlementTotal = differences
        .filter(d => d.action_type === 'settlement')
        .reduce((sum, d) => sum + parseFloat(d.difference), 0);
    
    const refundTotal = differences
        .filter(d => d.action_type === 'refund')
        .reduce((sum, d) => sum + parseFloat(d.difference), 0);
    
    document.getElementById('totalPending').textContent = differences.length;
    document.getElementById('totalSettlement').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(settlementTotal)}`;
    document.getElementById('totalRefund').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(refundTotal)}`;
}

function processPaymentDifference(differenceId, actionType) {
    // For now, show a message that processing modal should be opened
    // In actual implementation, this would open a specific modal to process the difference
    showInfoToast(`Memproses ${actionType === 'settlement' ? 'pelunasan' : 'pengembalian dana'} untuk selisih ID: ${differenceId}`);
    
    // You can add specific handling for each action type here
}

// Event listener untuk form submission
document.getElementById('differenceForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const paymentId = document.getElementById('diff_payment_id').value;
    
    // Fetch available transactions
    fetch('/bendahara/api/transactions')
        .then(response => response.json())
        .then(data => {
            const transactions = data.transactions;
            // Find a suitable income transaction
            const transaction = transactions.find(t => t.type === 'income' && !t.weekly_payment_id);
            
            if (!transaction) {
                showWarningToast('Tidak ada transaksi pemasukan yang tersedia untuk pelunasan selisih');
                return;
            }
            
            // Call the settlement endpoint
            fetch('/bendahara/api/process-settlement', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    difference_id: paymentId, // This might need adjustment
                    transaction_id: transaction.id
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSuccessToast('Selisih nominal berhasil dilunasi!');
                    closeDifferenceModal();
                    location.reload();
                } else {
                    showErrorToast(result.message || 'Gagal melunasi selisih nominal');
                }
            })
            .catch(error => {
                console.error('Error processing settlement:', error);
                showErrorToast('Terjadi kesalahan saat melunasi selisih nominal');
            });
        })
        .catch(error => {
            console.error('Error fetching transactions:', error);
            showErrorToast('Gagal mengambil data transaksi');
        });
});

// Toast notification functions
function showSuccessToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg z-[9999] animate-fade-in';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function showErrorToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-red-500 text-white rounded-lg shadow-lg z-[9999] animate-fade-in';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function showWarningToast(message, title = '', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-yellow-500 text-white rounded-lg shadow-lg z-[9999] animate-fade-in max-w-sm';
    let content = title ? `<div class="font-bold mb-1">${title}</div>` : '';
    content += message.split('\n').map(line => `<div>${line}</div>`).join('');
    toast.innerHTML = content;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
}

function showInfoToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-blue-500 text-white rounded-lg shadow-lg z-[9999] animate-fade-in';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Add CSS animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/bendahara/payment-differences-modal.blade.php ENDPATH**/ ?>
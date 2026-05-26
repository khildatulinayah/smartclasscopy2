# Implementation Guide - Payment Adjustment System

## Quick Start

### 1. Register Observer (Auto-Detect)

Edit [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php):

```php
<?php

namespace App\Providers;

use App\Models\KasSetting;
use App\Observers\KasSettingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register observer untuk auto-detect adjustment
        KasSetting::observe(KasSettingObserver::class);
    }
}
```

### 2. Run Migrations

```bash
php artisan migrate
```

Ini akan membuat:
- `payment_adjustments` table
- `student_credit_balances` table

### 3. Add Routes

Edit [routes/web.php](routes/web.php):

```php
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->group(function () {
    // ... routes lainnya ...

    // Payment Adjustment API
    Route::prefix('api/payment-adjustments')->group(function () {
        Route::get('/', [PaymentAdjustmentController::class, 'index'])
            ->name('payment-adjustments.index');
        
        Route::get('/{adjustment}', [PaymentAdjustmentController::class, 'show'])
            ->name('payment-adjustments.show');
        
        Route::post('/{adjustment}/process-shortage-invoice', 
            [PaymentAdjustmentController::class, 'processShortageAsInvoice'])
            ->name('payment-adjustments.process-shortage-invoice');
        
        Route::post('/{adjustment}/process-shortage-unpaid', 
            [PaymentAdjustmentController::class, 'processShortageAsUnpaid'])
            ->name('payment-adjustments.process-shortage-unpaid');
        
        Route::post('/{adjustment}/process-overpayment-credit', 
            [PaymentAdjustmentController::class, 'processOverpaymentAsCredit'])
            ->name('payment-adjustments.process-overpayment-credit');
        
        Route::post('/{adjustment}/process-overpayment-refund', 
            [PaymentAdjustmentController::class, 'processOverpaymentAsRefund'])
            ->name('payment-adjustments.process-overpayment-refund');
        
        Route::post('/{adjustment}/cancel', 
            [PaymentAdjustmentController::class, 'cancel'])
            ->name('payment-adjustments.cancel');
        
        Route::get('/summary', [PaymentAdjustmentController::class, 'summary'])
            ->name('payment-adjustments.summary');
    });

    // KasSetting dengan auto-adjust
    Route::post('kas-setting-update', 
        [PaymentAdjustmentController::class, 'updateKasSettingWithAdjustment'])
        ->name('kas-setting-update');

    // Credit Balances
    Route::get('api/student-credit-balances', 
        [PaymentAdjustmentController::class, 'creditBalances'])
        ->name('student-credit-balances.index');
});
```

### 4. Register Service Provider (Dependency Injection)

Di [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php):

```php
public function register(): void
{
    // Service binding
    $this->app->singleton(PaymentAdjustmentService::class);
    $this->app->singleton(KasSettingService::class);
}
```

---

## Usage Examples

### Example 1: Update KasSetting dengan Auto-Detect Adjustment

**Via Controller/API:**

```php
// POST /bendahara/kas-setting-update
{
  "month": 5,
  "year": 2026,
  "nominal": 8000
}

// Response:
{
  "success": true,
  "message": "Nominal kas berhasil diperbarui",
  "data": {
    "old_nominal": 6000,
    "new_nominal": 8000,
    "adjustments_created": 15,
    "adjustments": [
      {
        "id": 1,
        "student_name": "Budi Santoso",
        "type": "Kurang Bayar",
        "amount": 2000,
        "status": "Menunggu Diproses"
      },
      // ... more adjustments
    ]
  }
}
```

**Via Service (Programmatic):**

```php
use App\Services\KasSettingService;

$kasSettingService = app(KasSettingService::class);

$result = $kasSettingService->updateNominalWithAdjustmentDetection(
    month: 5,
    year: 2026,
    newNominal: 8000,
    detectedBy: auth()->user()
);

// result contains:
// - kas_setting: updated KasSetting model
// - adjustments: Collection of created PaymentAdjustment
// - old_nominal: previous nominal
// - new_nominal: new nominal
```

### Example 2: Process Shortage Adjustment

**As Invoice (Create separate transaction):**

```php
// POST /bendahara/api/payment-adjustments/{adjustment-id}/process-shortage-invoice
{
  "notes": "Siswa akan bayar minggu depan"
}

// Response:
{
  "success": true,
  "message": "Invoice berhasil dibuat",
  "data": {
    "adjustment": {
      "id": 1,
      "status": "processed",
      "handling_method": "invoice",
      "invoice_transaction_id": 123
    },
    "transaction": {
      "id": 123,
      "type": "income",
      "amount": 2000,
      "description": "Invoice penyesuaian kas: Minggu 1 Bulan 5/2026"
    }
  }
}
```

**As Unpaid (Add to student debt):**

```php
// POST /bendahara/api/payment-adjustments/{adjustment-id}/process-shortage-unpaid
{
  "notes": "Tambahkan ke tagihan bulanan"
}

// Response:
{
  "success": true,
  "message": "Kekurangan disimpan sebagai tagihan untuk pembayaran mendatang"
}
```

### Example 3: Process Overpayment Adjustment

**As Credit Balance (Simpan untuk penggunaan mendatang):**

```php
// POST /bendahara/api/payment-adjustments/{adjustment-id}/process-overpayment-credit
{
  "notes": "Disimpan sebagai saldo kredit"
}

// Response:
{
  "success": true,
  "message": "Kelebihan dibayar disimpan sebagai saldo kredit",
  "data": {
    "adjustment": {
      "id": 2,
      "status": "processed",
      "handling_method": "credit_balance"
    },
    "credit_balance": {
      "student_id": 5,
      "total_credit": 2000,
      "last_updated_at": "2026-05-25 10:30:00"
    }
  }
}
```

**As Refund (Return money immediately):**

```php
// POST /bendahara/api/payment-adjustments/{adjustment-id}/process-overpayment-refund
{
  "notes": "Uang sudah dikembalikan tunai"
}

// Response:
{
  "success": true,
  "message": "Transaksi pengembalian dana berhasil dibuat",
  "data": {
    "adjustment": {
      "id": 2,
      "status": "processed",
      "handling_method": "refund"
    },
    "transaction": {
      "id": 124,
      "type": "expense",
      "amount": 2000,
      "description": "Pengembalian dana penyesuaian kas: Minggu 1 Bulan 5/2026"
    }
  }
}
```

### Example 4: Query Adjustments

```php
use App\Models\PaymentAdjustment;

// Get all pending shortages
$shortages = PaymentAdjustment::pending()
    ->shortage()
    ->with('student', 'weeklyPayment')
    ->get();

// Group by student
$groupedByStudent = $shortages->groupBy('student_id')
    ->map(function ($adjustments) {
        return [
            'student' => $adjustments->first()->student->name,
            'total_shortage' => $adjustments->sum('adjustment_amount'),
            'count' => $adjustments->count(),
        ];
    });

// Get summary
$summary = PaymentAdjustment::pending()
    ->get()
    ->groupBy('adjustment_type')
    ->map(fn($items) => [
        'type' => $items->first()->adjustment_type_label,
        'total' => $items->count(),
        'amount' => $items->sum('adjustment_amount'),
    ]);

// Get credit balances
$credits = StudentCreditBalance::hasCredit()
    ->with('student')
    ->orderBy('total_credit', 'desc')
    ->get();
```

---

## UI/UX Recommendations

### 1. KasSetting Update Form

**Location:** Bendahara Dashboard / Pengaturan Kas

```html
<!-- Form Update Nominal Kas -->
<form id="kasSettingForm">
    <div class="form-group">
        <label>Bulan</label>
        <select name="month" required>
            <option value="">- Pilih Bulan -</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <!-- ... etc ... -->
        </select>
    </div>

    <div class="form-group">
        <label>Tahun</label>
        <input type="number" name="year" min="2020" max="2099" required>
    </div>

    <div class="form-group">
        <label>Nominal Baru (Rp)</label>
        <input type="number" name="nominal" min="1" required>
        <small>Nominal saat ini: <strong id="currentNominal">Rp -</strong></small>
    </div>

    <button type="submit" class="btn btn-primary">
        Perbarui Nominal
    </button>
</form>

<!-- Alert jika ada adjustment terdeteksi -->
<div id="adjustmentAlert" class="alert alert-warning" style="display: none;">
    <h5>⚠️ Penyesuaian Pembayaran Terdeteksi</h5>
    <p>
        Mengubah nominal dari <strong id="oldNominal"></strong> 
        menjadi <strong id="newNominal"></strong> akan membuat 
        <strong id="adjustmentCount"></strong> record penyesuaian.
    </p>
    <p>
        <a href="#adjustmentModal" class="btn btn-sm btn-warning">
            Lihat Detail Penyesuaian →
        </a>
    </p>
</div>
```

### 2. Adjustment List Dashboard

**Location:** Bendahara Dashboard / Penyesuaian Pembayaran

```html
<!-- Summary Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Kurang Bayar</h6>
                <h3 class="text-danger" id="totalShortage">Rp 0</h3>
                <small class="text-muted" id="shortageCount">0 penyesuaian</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Kelebihan Bayar</h6>
                <h3 class="text-success" id="totalOverpayment">Rp 0</h3>
                <small class="text-muted" id="overpaymentCount">0 penyesuaian</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Menunggu Proses</h6>
                <h3 class="text-warning" id="totalPending">0</h3>
                <small class="text-muted">adjustment</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Saldo Kredit</h6>
                <h3 class="text-info" id="totalCredit">Rp 0</h3>
                <small class="text-muted" id="studentWithCredit">siswa</small>
            </div>
        </div>
    </div>
</div>

<!-- Adjustment Table -->
<table class="table table-hover">
    <thead>
        <tr>
            <th>Siswa</th>
            <th>Minggu/Bulan</th>
            <th>Nominal</th>
            <th>Tipe</th>
            <th>Status</th>
            <th>Metode</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody id="adjustmentTableBody">
        <!-- Populated by JS -->
    </tbody>
</table>
```

### 3. Adjustment Processing Modal

```html
<!-- Modal Process Shortage -->
<div class="modal" id="processShortageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Kekurangan Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <strong id="studentName">-</strong> kurang bayar 
                    <strong id="shortageAmount">Rp 0</strong> 
                    untuk minggu <strong id="weekNumber">-</strong>
                </div>

                <!-- Method Selection -->
                <div class="form-group">
                    <label>Cara Penanganan:</label>
                    <div class="btn-group-vertical w-100" role="group">
                        <input type="radio" class="btn-check" name="method" value="invoice" id="methodInvoice">
                        <label class="btn btn-outline-primary text-start" for="methodInvoice">
                            <strong>📄 Invoice Terpisah</strong>
                            <br>
                            <small>Buat transaksi income terpisah</small>
                        </label>

                        <input type="radio" class="btn-check" name="method" value="unpaid" id="methodUnpaid">
                        <label class="btn btn-outline-primary text-start" for="methodUnpaid">
                            <strong>📋 Tambah Tagihan</strong>
                            <br>
                            <small>Disimpan sebagai kewajiban siswa</small>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group mt-3">
                    <label>Catatan (Opsional)</label>
                    <textarea id="shortageNotes" class="form-control" rows="3" 
                        placeholder="Catatan proses penyesuaian..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="submitShortageBtn">
                    Proses Penyesuaian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Process Overpayment -->
<div class="modal" id="processOverpaymentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Kelebihan Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-success">
                    <strong id="studentNameOvp">-</strong> kelebih bayar 
                    <strong id="overpaymentAmount">Rp 0</strong> 
                    untuk minggu <strong id="weekNumberOvp">-</strong>
                </div>

                <!-- Method Selection -->
                <div class="form-group">
                    <label>Cara Penanganan:</label>
                    <div class="btn-group-vertical w-100" role="group">
                        <input type="radio" class="btn-check" name="methodOvp" value="credit_balance" id="methodCredit">
                        <label class="btn btn-outline-success text-start" for="methodCredit">
                            <strong>💳 Saldo Kredit</strong>
                            <br>
                            <small>Simpan sebagai saldo kredit siswa</small>
                        </label>

                        <input type="radio" class="btn-check" name="methodOvp" value="refund" id="methodRefund">
                        <label class="btn btn-outline-success text-start" for="methodRefund">
                            <strong>💰 Pengembalian Dana</strong>
                            <br>
                            <small>Kembalikan uang ke siswa</small>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group mt-3">
                    <label>Catatan (Opsional)</label>
                    <textarea id="overpaymentNotes" class="form-control" rows="3" 
                        placeholder="Catatan proses penyesuaian..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-success" id="submitOverpaymentBtn">
                    Proses Penyesuaian
                </button>
            </div>
        </div>
    </div>
</div>
```

### 4. Credit Balance Report

```html
<!-- Student Credit Balance List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">💳 Saldo Kredit Siswa</h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Email</th>
                    <th>Saldo Kredit</th>
                    <th>Terakhir Update</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="creditBalanceTableBody">
                <!-- Populated by JS -->
            </tbody>
        </table>
    </div>
</div>
```

### 5. JavaScript untuk Interactive UI

```javascript
// Update KasSetting Form
document.getElementById('kasSettingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        month: document.querySelector('select[name="month"]').value,
        year: document.querySelector('input[name="year"]').value,
        nominal: document.querySelector('input[name="nominal"]').value,
    };

    try {
        const response = await fetch('/bendahara/kas-setting-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(formData),
        });

        const result = await response.json();

        if (result.success) {
            // Show alert dengan info adjustments
            document.getElementById('adjustmentAlert').style.display = 'block';
            document.getElementById('oldNominal').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(result.data.old_nominal);
            document.getElementById('newNominal').textContent = 'Rp ' + 
                new Intl.NumberFormat('id-ID').format(result.data.new_nominal);
            document.getElementById('adjustmentCount').textContent = 
                result.data.adjustments_created + ' penyesuaian';

            // Reload adjustment list
            loadAdjustmentList();

            // Show success toast
            showToast('success', result.message);
        } else {
            showToast('error', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan');
    }
});

// Load & display adjustments
async function loadAdjustmentList() {
    try {
        const response = await fetch('/bendahara/api/payment-adjustments?status=pending');
        const result = await response.json();

        if (result.success) {
            displayAdjustments(result.data.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayAdjustments(adjustments) {
    const tbody = document.getElementById('adjustmentTableBody');
    tbody.innerHTML = '';

    adjustments.forEach((adj, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${adj.student.name}</td>
            <td>Minggu ${adj.weeklyPayment.week_number} / ${adj.weeklyPayment.month}/${adj.weeklyPayment.year}</td>
            <td>${adj.adjustment_type_label}</td>
            <td>${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(adj.adjustment_amount)}</td>
            <td>${adj.status_label}</td>
            <td>${adj.handling_method_label}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editAdjustment(${adj.id})">
                    Proses
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function editAdjustment(adjustmentId) {
    // Fetch adjustment detail dan show appropriate modal
    fetch(`/bendahara/api/payment-adjustments/${adjustmentId}`)
        .then(r => r.json())
        .then(result => {
            if (result.data.adjustment_type === 'shortage') {
                showShortageModal(result.data);
            } else {
                showOverpaymentModal(result.data);
            }
        });
}
```

---

## Database Queries untuk Reporting

### Total Outstanding Shortage

```php
$totalShortage = PaymentAdjustment::pending()
    ->shortage()
    ->sum('adjustment_amount');
```

### Overpayment by Student

```php
$overpaymentByStudent = PaymentAdjustment::pending()
    ->overpayment()
    ->with('student')
    ->get()
    ->groupBy('student_id')
    ->map(function ($items) {
        return [
            'student' => $items->first()->student->name,
            'total' => abs($items->sum('adjustment_amount')),
        ];
    });
```

### Adjustment Processing History

```php
$processedAdjustments = PaymentAdjustment::processed()
    ->with('processedBy', 'student')
    ->whereBetween('processed_at', [$startDate, $endDate])
    ->orderBy('processed_at', 'desc')
    ->get();
```

### Student Credit Balance Report

```php
$creditReport = StudentCreditBalance::with('student')
    ->where('total_credit', '>', 0)
    ->orderBy('total_credit', 'desc')
    ->get();

$totalCredit = $creditReport->sum('total_credit');
```

---

## Best Practices & Tips

✅ **DO:**
- Selalu gunakan DB transaction untuk multi-step operations
- Log semua perubahan untuk audit trail
- Validate input sebelum process
- Provide clear feedback ke user
- Use eager loading untuk optimize queries

❌ **DON'T:**
- Jangan update `weekly_payment.amount` saat membuat adjustment
- Jangan delete adjustment records (soft delete jika perlu)
- Jangan bypass validation untuk "shortcut"
- Jangan assume user adalah bendahara (check role)

---

## Testing

### Unit Test Example

```php
use App\Models\PaymentAdjustment;
use App\Models\WeeklyPayment;
use App\Services\PaymentAdjustmentService;

class PaymentAdjustmentTest extends TestCase
{
    public function testDetectAndCreateAdjustments()
    {
        // Setup
        $payment = WeeklyPayment::factory()
            ->paid()
            ->amount(5000)
            ->create();

        $service = app(PaymentAdjustmentService::class);
        $user = User::factory()->create();

        // Execute
        $adjustments = $service->detectAndCreateAdjustments(
            month: $payment->month,
            year: $payment->year,
            newNominal: 7000,
            oldNominal: 5000,
            detectedBy: $user
        );

        // Assert
        $this->assertCount(1, $adjustments);
        $this->assertEquals('shortage', $adjustments->first()->adjustment_type);
        $this->assertEquals(2000, $adjustments->first()->adjustment_amount);
    }
}
```

---

## Troubleshooting

### Adjustment tidak terdeteksi saat update KasSetting

**Solusi:**
1. Check apakah Observer sudah registered di AppServiceProvider
2. Check apakah ada weekly_payment yang status='paid' untuk bulan tersebut
3. Check logs di storage/logs

### Transaction conflict ketika process adjustment

**Solusi:**
1. Pastikan tidak ada 2 request simultaneous untuk adjustment yang sama
2. Add unique constraint di database jika belum ada
3. Implement queue untuk batch processing

### Credit balance tidak match dengan overpayment

**Solusi:**
1. Verify adjustment_amount calculation
2. Check apakah ada manual credit updates yang tidak terecord
3. Run reconciliation query untuk audit

---

## Next Steps

1. ✅ Integrate dengan payment form untuk auto-use credit
2. ✅ Create notification system untuk pendahara
3. ✅ Add export report functionality
4. ✅ Implement audit log dengan activity tracking
5. ✅ Add approval workflow untuk processing

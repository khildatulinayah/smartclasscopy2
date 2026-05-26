# Sistem Penyesuaian Nominal Kas (Payment Adjustment System)

## 1. Ringkasan Konsep

Sistem ini dirancang untuk menangani perubahan nominal kas tanpa merusak histori pembayaran. Ketika nominal berubah, sistem membuat **adjustment record** terpisah yang mencatat selisih pembayaran, bukan mengubah data pembayaran lama.

### Prinsip Utama
- ✅ **Immutability**: Histori pembayaran tidak pernah diubah
- ✅ **Traceability**: Semua penyesuaian tercatat dengan jelas
- ✅ **Reconciliation**: Mudah melakukan rekonsiliasi keuangan
- ✅ **Scalability**: Desain mendukung pertumbuhan sistem

---

## 2. Desain Database

### Tabel: `payment_adjustments`

```sql
CREATE TABLE payment_adjustments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- FK & Identitas
    weekly_payment_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    
    -- Data Nominal
    original_amount DECIMAL(15, 2) NOT NULL,        -- Nominal saat pembayaran
    current_nominal DECIMAL(15, 2) NOT NULL,        -- Nominal terbaru dari KasSetting
    adjustment_amount DECIMAL(15, 2) NOT NULL,      -- Selisih (positive=shortage, negative=overpayment)
    adjustment_type ENUM('shortage', 'overpayment') NOT NULL,
    
    -- Status & Penanganan
    status ENUM('pending', 'processed', 'cancelled') DEFAULT 'pending',
    handling_method ENUM('unpaid', 'invoice', 'credit_balance', 'refund') DEFAULT 'unpaid',
    
    -- Transaksi Terkait
    invoice_transaction_id BIGINT UNSIGNED NULL,    -- Untuk shortage invoice
    refund_transaction_id BIGINT UNSIGNED NULL,     -- Untuk overpayment refund
    credit_transaction_id BIGINT UNSIGNED NULL,     -- Untuk overpayment credit
    
    -- Audit
    detected_by BIGINT UNSIGNED NOT NULL,           -- User yang mendeteksi (bendahara)
    processed_by BIGINT UNSIGNED NULL,              -- User yang memproses
    notes TEXT NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    processed_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (weekly_payment_id) REFERENCES weekly_payments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (detected_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    FOREIGN KEY (refund_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    FOREIGN KEY (credit_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    
    -- Indexes
    UNIQUE KEY unique_adjustment_per_payment (weekly_payment_id),
    INDEX idx_student_status (student_id, status),
    INDEX idx_created_at (created_at),
    INDEX idx_adjustment_type (adjustment_type),
    INDEX idx_handling_method (handling_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabel: `student_credit_balances` (Opsional, untuk tracking saldo kredit)

```sql
CREATE TABLE student_credit_balances (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL UNIQUE,
    total_credit DECIMAL(15, 2) DEFAULT 0,
    last_updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_total_credit (total_credit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Entity Relationship Diagram

```
┌─────────────────────────────────────┐
│ weekly_payments (Immutable)          │
├─────────────────────────────────────┤
│ id                                   │
│ student_id → users                   │
│ amount (original amount)             │◄────┐
│ status (paid/unpaid)                 │     │
│ transaction_id → transactions        │     │
│ payment_date                         │     │
└─────────────────────────────────────┘     │
                                             │
                                             │ 1:1 relationship
                                             │
┌─────────────────────────────────────┐     │
│ payment_adjustments (Adjustment)     │     │
├─────────────────────────────────────┤     │
│ id                                   │─────┘
│ weekly_payment_id (FK) ──────────────┤
│ original_amount                      │ ◄─── Historical data
│ current_nominal                      │
│ adjustment_amount (difference)       │
│ adjustment_type                      │
│ status                               │
│ handling_method                      │
│ invoice_transaction_id ──┐           │
│ refund_transaction_id ────┼──► transactions
│ credit_transaction_id ───┘           │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ users (Student)                      │
├─────────────────────────────────────┤
│ id                                   │
│ name                                 │
│ email                                │
└─────────────────────────────────────┘

┌──────────────────────────────────────┐
│ student_credit_balances (Ledger)     │
├──────────────────────────────────────┤
│ student_id ────────────────────────► │ users
│ total_credit                         │
└──────────────────────────────────────┘
```

---

## 4. Model Relationships

### PaymentAdjustment Model

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_payment_id',
        'student_id',
        'original_amount',
        'current_nominal',
        'adjustment_amount',
        'adjustment_type',
        'status',
        'handling_method',
        'invoice_transaction_id',
        'refund_transaction_id',
        'credit_transaction_id',
        'detected_by',
        'processed_by',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_nominal' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function weeklyPayment(): BelongsTo
    {
        return $this->belongsTo(WeeklyPayment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function detectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'detected_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function invoiceTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'invoice_transaction_id');
    }

    public function refundTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'refund_transaction_id');
    }

    public function creditTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'credit_transaction_id');
    }

    // ========== SCOPES ==========

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeShortage($query)
    {
        return $query->where('adjustment_type', 'shortage');
    }

    public function scopeOverpayment($query)
    {
        return $query->where('adjustment_type', 'overpayment');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByHandlingMethod($query, $method)
    {
        return $query->where('handling_method', $method);
    }

    // ========== ACCESSORS & MUTATORS ==========

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Diproses',
            'processed' => 'Sudah Diproses',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    public function getAdjustmentTypeLabelAttribute(): string
    {
        return match($this->adjustment_type) {
            'shortage' => 'Kurang Bayar',
            'overpayment' => 'Kelebihan Bayar',
            default => 'Tidak Diketahui',
        };
    }

    public function getHandlingMethodLabelAttribute(): string
    {
        return match($this->handling_method) {
            'unpaid' => 'Tagihan',
            'invoice' => 'Invoice Terpisah',
            'credit_balance' => 'Kredit Saldo',
            'refund' => 'Pengembalian Dana',
            default => 'Tidak Diketahui',
        };
    }

    // ========== BUSINESS LOGIC METHODS ==========

    /**
     * Mark adjustment sebagai processed
     */
    public function markAsProcessed(User $processedBy, ?string $notes = null): void
    {
        $this->update([
            'status' => 'processed',
            'processed_by' => $processedBy->id,
            'processed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);

        // Log activity jika menggunakan activity log
        activity()
            ->causedBy($processedBy)
            ->performedOn($this)
            ->log("Memproses penyesuaian pembayaran: {$this->adjustment_type_label}");
    }

    /**
     * Mark adjustment sebagai cancelled
     */
    public function markAsCancelled(User $cancelledBy, string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => "Dibatalkan: {$reason}",
        ]);
    }

    /**
     * Check apakah adjustment bisa diproses
     */
    public function canBeProcessed(): bool
    {
        return $this->status === 'pending' && 
               $this->weeklyPayment()->exists();
    }

    /**
     * Get related transaction for this adjustment based on handling method
     */
    public function getRelatedTransaction(): ?Transaction
    {
        return match($this->handling_method) {
            'invoice' => $this->invoiceTransaction,
            'refund' => $this->refundTransaction,
            'credit_balance' => $this->creditTransaction,
            default => null,
        };
    }
}
```

### WeeklyPayment Model Update

```php
// Tambahkan relationship ini ke WeeklyPayment model

public function adjustment()
{
    return $this->hasOne(PaymentAdjustment::class, 'weekly_payment_id');
}

public function hasAdjustment(): bool
{
    return $this->adjustment()->exists();
}

public function isPendingAdjustment(): bool
{
    return $this->adjustment()
        ->where('status', 'pending')
        ->exists();
}

public function getAdjustmentStatus(): ?string
{
    return $this->adjustment?->status;
}

public function getAdjustmentType(): ?string
{
    return $this->adjustment?->adjustment_type;
}
```

### StudentCreditBalance Model

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCreditBalance extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'total_credit', 'last_updated_at'];

    protected $casts = [
        'total_credit' => 'decimal:2',
        'last_updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Add credit to balance
     */
    public function addCredit(decimal $amount): void
    {
        $this->increment('total_credit', $amount);
        $this->update(['last_updated_at' => now()]);
    }

    /**
     * Use credit from balance
     */
    public function useCredit(decimal $amount): bool
    {
        if ($this->total_credit >= $amount) {
            $this->decrement('total_credit', $amount);
            $this->update(['last_updated_at' => now()]);
            return true;
        }
        return false;
    }

    /**
     * Get or create balance for student
     */
    public static function forStudent(User $student): self
    {
        return static::firstOrCreate(
            ['student_id' => $student->id],
            ['total_credit' => 0]
        );
    }
}
```

---

## 5. Status & Enum Values

### Adjustment Type
- **shortage** (Kurang Bayar): Nominal baru > nominal lama
- **overpayment** (Kelebihan Bayar): Nominal baru < nominal lama

### Adjustment Status
- **pending** (Menunggu): Baru dideteksi, belum diproses
- **processed** (Diproses): Sudah ditangani sesuai handling_method
- **cancelled** (Dibatalkan): Dibatalkan karena alasan tertentu

### Handling Method
- **unpaid** (Tagihan): Ditambahkan ke tagihan siswa (default untuk shortage)
- **invoice** (Invoice Terpisah): Dibuat invoice transaksi terpisah
- **credit_balance** (Kredit Saldo): Disimpan sebagai saldo kredit siswa (default untuk overpayment)
- **refund** (Pengembalian Dana): Langsung dikembalikan via transaksi

---

## 6. Alur Kerja Sistem

### Skenario 1: Kurang Bayar (Shortage)

```
1. Siswa bayar Rp 5.000 (nominal saat itu)
   → weekly_payment: amount = 5.000, status = 'paid'

2. Bendahara ubah nominal KasSetting → Rp 7.000

3. Sistem deteksi:
   - Cari semua weekly_payment yang sudah paid
   - Bandingkan dengan nominal baru
   - Jika ada selisih: buat PaymentAdjustment

4. PaymentAdjustment dibuat:
   - adjustment_type = 'shortage'
   - adjustment_amount = 2.000 (Rp 7.000 - Rp 5.000)
   - handling_method = 'unpaid' (default)
   - status = 'pending'

5. Bendahara pilih handling method:
   - Option A: Biarkan jadi tagihan biasa
   - Option B: Buat invoice transaksi terpisah
   - Option C: Siswa bayar minggu depan

6. Jika dipilih "Invoice Terpisah":
   - Buat Transaction: type='income', amount=2.000
   - Link ke adjustment via invoice_transaction_id
   - Siswa bayar via normal payment flow

7. Setelah dibayar:
   - Bendahara mark adjustment as processed
   - Status → 'processed'
   - processed_at → current timestamp
```

### Skenario 2: Kelebihan Bayar (Overpayment)

```
1. Siswa bayar Rp 7.000 (nominal saat itu)
   → weekly_payment: amount = 7.000, status = 'paid'

2. Bendahara ubah nominal KasSetting → Rp 5.000

3. Sistem deteksi:
   - adjustment_type = 'overpayment'
   - adjustment_amount = -2.000 (Rp 5.000 - Rp 7.000)
   - handling_method = 'credit_balance' (default)
   - status = 'pending'

4. Bendahara pilih handling method:
   - Option A: Simpan sebagai kredit saldo (default)
   - Option B: Kembalikan via cash
   - Option C: Siswa gunakan di bulan depan

5. Jika dipilih "Simpan Kredit Saldo":
   - StudentCreditBalance += 2.000
   - Adjustment → processed
   - Siswa bisa pakai di pembayaran mendatang

6. Jika dipilih "Kembalikan Dana":
   - Buat Transaction: type='expense', amount=2.000
   - Link ke adjustment via refund_transaction_id
   - Bendahara catat bukti pengembalian
```

---

## 7. Konvensi & Best Practices

### Error Handling
- Semua operasi adjustment harus dalam transaction
- Jika gagal, rollback dan log error
- Notifikasi bendahara jika ada error

### Audit Trail
- Semua perubahan log via detected_by & processed_by
- Gunakan Laravel's activity log jika ada
- Simpan notes untuk dokumentasi

### Validation
- adjustment_amount harus sesuai dengan: `current_nominal - original_amount`
- Hanya adjustment.pending yang bisa diproses
- Transaksi harus exist sebelum mark as processed

### Performance
- Index pada: `(student_id, status)`, `(created_at)`, `(adjustment_type)`
- Gunakan eager loading: `with('student', 'weeklyPayment')`
- Batch process untuk multiple adjustments

### Immutability
- **JANGAN** update weekly_payment.amount saat membuat adjustment
- Adjustment adalah historical record terpisah
- Query reporting harus join kedua tabel

---

## 8. Contoh Queries

### Daftar adjustment per siswa
```php
PaymentAdjustment::byStudent($studentId)
    ->pending()
    ->with('weeklyPayment', 'student')
    ->get();
```

### Total shortage yang belum diproses per kelas
```php
PaymentAdjustment::shortage()
    ->pending()
    ->with('student')
    ->get()
    ->groupBy('student.class')
    ->map(fn($group) => [
        'class' => $group[0]->student->class,
        'total' => $group->sum('adjustment_amount'),
        'count' => $group->count(),
    ]);
```

### Rekonsiliasi untuk laporan bulanan
```php
PaymentAdjustment::whereBetween('created_at', [$startDate, $endDate])
    ->with('student', 'weeklyPayment')
    ->get()
    ->groupBy('adjustment_type');
```

---

## 9. Next Steps

1. ✅ Buat migration
2. ✅ Buat models dengan relationships
3. ✅ Buat Service class untuk business logic
4. ✅ Update KasSetting listener/observer
5. ✅ Buat controller endpoints
6. ✅ Buat blade templates
7. ✅ Buat unit tests

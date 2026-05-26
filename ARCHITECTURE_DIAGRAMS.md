# Architecture & Flow Diagrams - Payment Adjustment System

## 1. System Architecture

### Component Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         Laravel Application                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐         ┌────────────────────┐                │
│  │  Controllers │         │   Service Layer    │                │
│  ├──────────────┤         ├────────────────────┤                │
│  │ Payment      │────────▶│ Payment            │                │
│  │ Adjustment   │         │ AdjustmentService  │                │
│  │ Controller   │         │                    │                │
│  │              │         │ KasSettingService  │                │
│  └──────────────┘         └────────────────────┘                │
│          ▲                           │                           │
│          │                           ▼                           │
│          │      ┌────────────────────────────────┐              │
│          │      │    Business Logic Layer        │              │
│          │      ├────────────────────────────────┤              │
│          │      │ - Detect adjustments           │              │
│          │      │ - Process by handling method   │              │
│          │      │ - Manage credit balance        │              │
│          │      │ - Generate reports             │              │
│          │      └────────────────────────────────┘              │
│          │                   │                                   │
│          │                   ▼                                   │
│  ┌──────────────────────────────────────────┐                  │
│  │          Model Layer                      │                  │
│  ├──────────────────────────────────────────┤                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ PaymentAdjustment                    │ │                  │
│  │ │ - Original data immutable            │ │                  │
│  │ │ - Status & handling method           │ │                  │
│  │ │ - Audit trail (detected_by,etc)      │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  │                                            │                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ StudentCreditBalance                 │ │                  │
│  │ │ - Track credit per student           │ │                  │
│  │ │ - Add/use credit methods             │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  │                                            │                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ WeeklyPayment (updated)              │ │                  │
│  │ │ - Has one adjustment relationship    │ │                  │
│  │ │ - Check adjustment methods           │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  │                                            │                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ Transaction (existing)               │ │                  │
│  │ │ - Link untuk invoice/refund          │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  └──────────────────────────────────────────┘                  │
│                      │                                          │
│                      ▼                                          │
│  ┌──────────────────────────────────────────┐                  │
│  │      Database Layer (MySQL)               │                  │
│  ├──────────────────────────────────────────┤                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ payment_adjustments table            │ │                  │
│  │ │ - 1-to-1 dengan weekly_payments      │ │                  │
│  │ │ - FK ke users, transactions          │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  │                                            │                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ student_credit_balances table        │ │                  │
│  │ │ - 1-to-1 dengan users                │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  │                                            │                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ weekly_payments (existing)           │ │                  │
│  │ │ - Immutable, tidak diubah            │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  └──────────────────────────────────────────┘                  │
│                                                                   │
│  ┌──────────────────────────────────────────┐                  │
│  │      Event & Observer Layer              │                  │
│  ├──────────────────────────────────────────┤                  │
│  │ ┌──────────────────────────────────────┐ │                  │
│  │ │ KasSettingObserver                   │ │                  │
│  │ │ - Listen ke KasSetting.updated event │ │                  │
│  │ │ - Auto-trigger adjustment detection  │ │                  │
│  │ └──────────────────────────────────────┘ │                  │
│  └──────────────────────────────────────────┘                  │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Sequence Diagram - Update Nominal & Auto-Detect

```
Bendahara          UI              API              Service          DB
   │               │               │                  │               │
   │─ Input form──▶│               │                  │               │
   │ (nominal)    │               │                  │               │
   │               │─ POST /---────▶│                  │               │
   │               │ kas-setting-   │                  │               │
   │               │ update         │                  │               │
   │               │                │─ Validate──────▶│               │
   │               │                │ Nominal         │               │
   │               │◀───Validation ─│                  │               │
   │               │  Response      │                  │               │
   │               │                │                  │               │
   │               │                │─ updateWithAdj  │               │
   │               │                │ (month, year,   │               │
   │               │                │  nominal)       │               │
   │               │                │                  │ BEGIN         │
   │               │                │                  │ TRANSACTION   │
   │               │                │                  │◀──────────────│
   │               │                │                  │               │
   │               │                │                  │─ Get old───→│
   │               │                │                  │ nominal    │
   │               │                │                  │            │
   │               │                │                  │◀──────────│
   │               │                │                  │           │
   │               │                │                  │─ Update───→│
   │               │                │                  │ KasSetting │
   │               │                │                  │◀──────────│
   │               │                │                  │           │
   │               │                │                  │─ Find paid ─→│
   │               │                │                  │ payments   │
   │               │                │                  │            │
   │               │                │                  │◀──────────│
   │               │                │                  │           │
   │               │                │                  │─ For each payment:
   │               │                │                  │  Create Adjustment
   │               │                │                  │         │
   │               │                │                  │─────────→│
   │               │                │                  │ INSERT   │
   │               │                │                  │ payment_ │
   │               │                │                  │ adjustm. │
   │               │                │                  │          │
   │               │                │                  │◀────────│
   │               │                │                  │          │
   │               │                │                  │─ COMMIT─→│
   │               │                │                  │          │
   │               │                │                  │◀────────│
   │               │                │                  │          │
   │               │◀─ 200 OK ──────│                  │          │
   │               │ + Adjustments │                  │          │
   │               │ Summary       │                  │          │
   │               │               │                  │          │
   │◀─ Display results
   │               │
```

---

## 3. State Machine - Adjustment Status Flow

```
┌─────────────┐
│   PENDING   │◄──── Created (auto-detect)
│  (Awaiting  │
│  Process)   │
└──────┬──────┘
       │
       ├──────┬──────────────┬─────────────┐
       │      │              │             │
       ▼      ▼              ▼             ▼
   [Invoice] [Unpaid]  [Credit]      [Refund]
   [Process] [Process] [Process]      [Process]
       │      │              │             │
       └──────┴──────────────┴─────────────┘
              │
              ▼
       ┌─────────────┐
       │ PROCESSED   │
       │ (Handled)   │
       └─────────────┘


Alternative: Cancellation Path
┌─────────────┐
│   PENDING   │
└──────┬──────┘
       │
       ├─── [Invalid]
       │    [Cancel]
       │
       ▼
┌─────────────┐
│ CANCELLED   │
│ (Rejected)  │
└─────────────┘
```

---

## 4. Process Flow - Shortage Adjustment

```
START: Nomina Berubah (Naik)

    ┌─────────────────────────────┐
    │ BendaharaSave KasSetting     │
    │ old_nominal = 6000          │
    │ new_nominal = 8000          │
    └──────────┬──────────────────┘
               │
    KasSettingObserver.updating()
    $__originalNominal = 6000
               │
    KasSettingObserver.updated()
               │
    ┌──────────▼──────────────────────────┐
    │ detectAndCreateAdjustments()         │
    │ - month: 5, year: 2026              │
    │ - oldNominal: 6000                  │
    │ - newNominal: 8000                  │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Find WeeklyPayment                  │
    │ WHERE status='paid'                 │
    │ AND month=5 AND year=2026           │
    │ RESULT: 15 students                 │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────────┐
    │ FOR EACH payment:                       │
    │                                         │
    │ ┌────────────────────────────────────┐ │
    │ │ Hitung selisih:                    │ │
    │ │ adjustment = 8000 - 6000 = 2000   │ │
    │ │ tipe = 'shortage' (positif)       │ │
    │ │ handling_method = 'unpaid'        │ │
    │ │                                   │ │
    │ │ Create PaymentAdjustment:         │ │
    │ │ - original_amount: 6000           │ │
    │ │ - current_nominal: 8000           │ │
    │ │ - adjustment_amount: 2000         │ │
    │ │ - status: 'pending'               │ │
    │ │ - detected_by: $authUser          │ │
    │ └────────────────────────────────────┘ │
    │                                         │
    │ × 15 students                          │
    └──────────┬──────────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Return: 15 PaymentAdjustment       │
    │ Total Shortage: Rp 30.000          │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Show Alert to Bendahara:            │
    │ "15 Penyesuaian Terdeteksi"         │
    │ "Total Kurang Bayar: Rp 30.000"     │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Bendahara View Pending Adjustments  │
    │ Show list dengan options:           │
    │ - Buat Invoice Terpisah             │
    │ - Tambah Tagihan Biasa              │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Bendahara Pilih: Invoice Terpisah   │
    │ Klik "Proses" untuk adjustment 1    │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ POST /api/adjustment/{id}/process-        │
    │ shortage-invoice                          │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ PaymentAdjustmentService.                  │
    │ processShortageAsInvoice()                │
    │                                            │
    │ BEGIN TRANSACTION                        │
    │  1. Create Transaction (income):         │
    │     - type: 'income'                     │
    │     - amount: 2000                       │
    │     - description: "Invoice penyesuaian" │
    │     - date: now()                        │
    │                                          │
    │  2. Update PaymentAdjustment:            │
    │     - invoice_transaction_id: TX_ID      │
    │     - handling_method: 'invoice'        │
    │                                          │
    │  3. markAsProcessed():                   │
    │     - status: 'processed'                │
    │     - processed_by: $bendahara           │
    │     - processed_at: now()                │
    │                                          │
    │ COMMIT TRANSACTION                      │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ Show Success:                             │
    │ "Invoice berhasil dibuat"                 │
    │ Adjustment status: PROCESSED              │
    │ Transaction created: #TX_ID               │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ Siswa akan bayar invoice Rp 2000 ini      │
    │ seperti pembayaran kas normal lainnya     │
    └────────────────────────────────────────────┘

END: Shortage terhandle, history tetap immutable
```

---

## 5. Process Flow - Overpayment Adjustment

```
START: Nominal Berubah (Turun)

    ┌─────────────────────────────┐
    │ Bendahara Save KasSetting    │
    │ old_nominal = 8000          │
    │ new_nominal = 6000          │
    └──────────┬──────────────────┘
               │
    KasSettingObserver detects
    adjustment_amount = 6000 - 8000 = -2000
    type = 'overpayment'
               │
    ┌──────────▼──────────────────────────┐
    │ Create 15 PaymentAdjustment:         │
    │ - type: 'overpayment'               │
    │ - handling_method: 'credit_balance' │
    │ - status: 'pending'                 │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼──────────────────────────┐
    │ Bendahara View Adjustments           │
    │ Show: "Kelebihan Bayar Terdeteksi"  │
    │ Total: Rp 30.000 (15 × Rp 2.000)    │
    └──────────┬──────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ Bendahara Pilih Handling Method:          │
    │                                           │
    │ Option A: Saldo Kredit (default)         │
    │ Option B: Pengembalian Dana              │
    │                                           │
    │ Pilih: Option A (Saldo Kredit)           │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ POST /api/adjustment/{id}/process-       │
    │ overpayment-credit                       │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ PaymentAdjustmentService.                 │
    │ processOverpaymentAsCredit()             │
    │                                           │
    │ BEGIN TRANSACTION                       │
    │  1. StudentCreditBalance.forStudent()   │
    │     - Get or create balance              │
    │                                          │
    │  2. addCredit(2000):                    │
    │     - total_credit += 2000              │
    │     - last_updated_at = now()           │
    │                                          │
    │  3. Update PaymentAdjustment:           │
    │     - handling_method: 'credit_balance' │
    │                                          │
    │  4. markAsProcessed():                  │
    │     - status: 'processed'               │
    │                                          │
    │ COMMIT TRANSACTION                     │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ Show Success:                             │
    │ "Saldo kredit berhasil disimpan"         │
    │ Siswa sekarang punya kredit: Rp 2.000   │
    │ Bisa dipakai untuk pembayaran mendatang │
    └──────────┬────────────────────────────────┘
               │
    ┌──────────▼────────────────────────────────┐
    │ Saat Siswa Bayar Minggu Depan:           │
    │ - Tagihan minggu = Rp 6.000              │
    │ - Siswa punya kredit = Rp 2.000          │
    │ - Tagihan akhir = Rp 4.000 (Rp 6-2)    │
    │ - Kredit sisa = Rp 0                     │
    └────────────────────────────────────────────┘

END: Overpayment disimpan, bisa digunakan nanti
```

---

## 6. Data Flow - Immutability Principle

```
┌─────────────────────────────────────────────────────────┐
│          WEEKLY PAYMENT (Original Payment)              │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  student_id: 5                                          │
│  week_number: 1                                         │
│  month: 5                                               │
│  year: 2026                                             │
│  amount: 6000 ◄──── IMMUTABLE! Tidak pernah diubah    │
│  status: 'paid'                                         │
│  payment_date: 2026-05-10                              │
│  created_at: 2026-05-10                                │
│  updated_at: 2026-05-10 ◄── Tidak berubah sejak create
│                                                         │
└─────────────────────────────────────────────────────────┘
                           │
                           │ Relasi 1:1
                           ▼
┌─────────────────────────────────────────────────────────┐
│        PAYMENT ADJUSTMENT (Adjustment Record)           │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  weekly_payment_id: 1 ◄── Reference only               │
│  student_id: 5                                          │
│  original_amount: 6000 ◄── Historical snapshot         │
│  current_nominal: 8000 ◄── Snapshot saat adjustment   │
│  adjustment_amount: 2000 ◄── Calculated difference    │
│  adjustment_type: 'shortage'                           │
│  status: 'pending' ▶ 'processed'                      │
│  handling_method: 'unpaid' ▶ 'invoice'               │
│  invoice_transaction_id: NULL ▶ 123                   │
│  detected_by: 1 (bendahara)                            │
│  processed_by: NULL ▶ 2 (bendahara)                   │
│  created_at: 2026-05-25                                │
│  updated_at: 2026-05-25 ▶ 2026-05-26                 │
│                                                         │
└─────────────────────────────────────────────────────────┘

PENTING:
❌ JANGAN: UPDATE weekly_payment.amount
✅ BENAR:  Simpan semua info di PaymentAdjustment

Benefit Immutability:
1. Audit trail lengkap (original vs current)
2. Laporan akurat (tidak ada perubahan data asli)
3. Reconciliation mudah (bandingkan adjustment)
4. Legal compliant (historical data preserved)
```

---

## 7. Integration Points

### With Existing System

```
┌────────────────────────────────────────────────────┐
│         Existing System (Before)                    │
├────────────────────────────────────────────────────┤
│                                                     │
│  WeeklyPayment          Transaction                │
│  - amount               - type (income/expense)    │
│  - status               - amount                   │
│  - payment_date         - date                     │
│  - transaction_id       - student_id               │
│                                                     │
│              KasSetting                            │
│              - month                               │
│              - year                                │
│              - nominal                             │
│                                                     │
│  ❌ Masalah: Nominal berubah, payment tidak sync  │
│                                                    │
└────────────────────────────────────────────────────┘
                           │
                           │ NEW: Tambahkan Layer Adjustment
                           ▼
┌────────────────────────────────────────────────────┐
│     New Payment Adjustment System                   │
├────────────────────────────────────────────────────┤
│                                                     │
│  PaymentAdjustment                                 │
│  - Links WeeklyPayment + KasSetting               │
│  - Detects differences                             │
│  - Tracks handling method                          │
│  - Creates related transactions                    │
│                                                     │
│  StudentCreditBalance                              │
│  - Manages overpayment credits                     │
│  - Tracks usage                                    │
│                                                     │
│  ✅ Solution: Adjustment recorded separately       │
│     Weekly payment immutable                      │
│     Full audit trail preserved                    │
│                                                    │
└────────────────────────────────────────────────────┘
```

---

## 8. Error Handling Flow

```
User Action
    │
    ▼
┌─────────────────────────┐
│ Validate Input           │
└────┬────────────────────┘
     │
     ├─ Invalid ──▶ Return Error 422
     │
     └─ Valid
          │
          ▼
┌─────────────────────────────────────┐
│ Check Adjustment Eligibility         │
├─────────────────────────────────────┤
│ - Status must be 'pending'           │
│ - Type matches operation             │
│ - No conflicting transaction         │
└────┬────────────────────────────────┘
     │
     ├─ Invalid ──▶ Return Error 422
     │
     └─ Valid
          │
          ▼
┌─────────────────────────────────────┐
│ Begin Database Transaction           │
└────┬────────────────────────────────┘
     │
     ├─ Transaction Error ──▶ Rollback ──▶ Error 500
     │
     └─ Success
          │
          ▼
┌─────────────────────────────────────┐
│ Process Adjustment                   │
│ (Create transactions, update balance)│
└────┬────────────────────────────────┘
     │
     ├─ Validation Error ──▶ Rollback ──▶ Error 422
     ├─ Constraint Error ──▶ Rollback ──▶ Error 409
     │
     └─ Success
          │
          ▼
┌─────────────────────────────────────┐
│ Commit Transaction                   │
└────┬────────────────────────────────┘
     │
     ▼
┌─────────────────────────────────────┐
│ Return Success Response              │
│ + Updated Data                       │
└─────────────────────────────────────┘
```

---

## 9. Scalability Considerations

### Performance Optimization

```
1. Bulk Detection
   ├─ Jangan detect per payment, detect per KasSetting change
   ├─ 1 query untuk find all paid payments
   ├─ Batch insert ke payment_adjustments
   └─ Result: O(n) bukan O(n²)

2. Indexing Strategy
   ├─ (student_id, status) - filter by student & status
   ├─ (created_at) - sorting & date range queries
   ├─ (adjustment_type) - filter by shortage/overpayment
   └─ (weekly_payment_id) UNIQUE - ensure 1:1 relationship

3. Query Optimization
   ├─ Use eager loading for relationships
   ├─ Projection (select only needed columns)
   ├─ Pagination untuk large result sets
   ├─ Cache frequently accessed data
   └─ Use materialized views untuk complex reports

4. Concurrent Operations
   ├─ Lock adjustment records saat process
   ├─ Use pessimistic locking untuk consistency
   └─ Queue untuk batch processing
```

### Future Enhancements

```
1. Approval Workflow
   ├─ Pendahara detects adjustment
   ├─ Submit untuk approval
   ├─ Manager approve/reject
   └─ Auto-create transaction setelah approved

2. Automated Credit Usage
   ├─ Saat siswa bayar, auto-use available credit
   ├─ Reduce tagihan otomatis
   └─ Log credit usage di transaction

3. Notification System
   ├─ Alert bendahara saat adjustment terdeteksi
   ├─ Notify siswa tentang kekurangan/kelebihan
   └─ Reminder untuk pending adjustments

4. API Integration
   ├─ Webhook untuk payment system
   ├─ Real-time sync dengan accounting software
   └─ Export ke format standar (CSV, PDF, etc)
```

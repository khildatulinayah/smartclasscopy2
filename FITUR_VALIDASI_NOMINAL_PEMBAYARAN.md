# Dokumentasi Fitur: Validasi Nominal Pembayaran Mingguan

## Ringkasan Fitur
Sistem ini mengimplementasikan fitur validasi dan pendeteksian selisih nominal pembayaran kas siswa ketika terjadi perubahan nominal kas setelah siswa melakukan pembayaran. Sistem dapat mendeteksi:
1. **Pelunasan (Settlement)**: Ketika nominal baru lebih besar dari nominal lama, siswa perlu melunasi kekurangan
2. **Pengembalian Dana (Refund)**: Ketika nominal baru lebih kecil dari nominal lama, bendahara perlu mengembalikan kelebihan dana

## Komponen Sistem

### 1. Database & Migration
**File**: `database/migrations/2026_05_21_000000_create_payment_differences_table.php`

Tabel `payment_differences` menyimpan data selisih nominal dengan struktur:
- `weekly_payment_id`: FK ke tabel weekly_payments
- `student_id`: FK ke tabel users
- `old_nominal`: Nominal yang dibayarkan (saat pembayaran)
- `new_nominal`: Nominal baru dari settings
- `difference`: Selisih yang perlu diselesaikan (positif atau negatif)
- `status`: pending, settled, refunded
- `action_type`: settlement atau refund
- `settlement_transaction_id`: FK ke transaksi penyelesaian
- `settlement_date`: Tanggal penyelesaian
- `notes`: Catatan/deskripsi
- `created_by`, `processed_by`: FK ke users

### 2. Models

#### PaymentDifference Model
**File**: `app/Models/PaymentDifference.php`

Fitur utama:
- Relasi dengan WeeklyPayment, User (student, creator, processor), dan Transaction
- Scopes: pending(), settled(), refunded(), needsSettlement(), needsRefund()
- Methods: markAsSettled(), markAsRefunded()
- Attributes: getStatusLabelAttribute(), getActionTypeLabelAttribute()

#### WeeklyPayment Model Updates
**File**: `app/Models/WeeklyPayment.php`

Penambahan fitur:
- Relasi: paymentDifferences(), activeDifference()
- Method checkPaymentDifference(): Validasi apakah ada perbedaan nominal
- Method detectAndCreateDifference(): Deteksi dan buat payment_differences record
- Accessor: has_difference, difference_status

### 3. Controller
**File**: `app/Http/Controllers/BendaharaController.php`

API Endpoints baru:
- `GET /bendahara/api/payment-differences`: Ambil daftar selisih (filter by status, student, type)
- `POST /bendahara/api/check-payment-difference`: Deteksi dan buat record selisih
- `POST /bendahara/api/process-settlement`: Proses pelunasan (settlement)
- `POST /bendahara/api/process-refund`: Proses pengembalian dana (refund)
- `GET /bendahara/api/payment-difference-summary`: Ringkasan selisih per siswa

### 4. Routes
**File**: `routes/web.php`

Routes yang ditambahkan dalam group bendahara:
```php
Route::get('/api/payment-differences', ...)->name('api.payment_differences');
Route::post('/api/check-payment-difference', ...)->name('api.check_payment_difference');
Route::post('/api/process-settlement', ...)->name('api.process_settlement');
Route::post('/api/process-refund', ...)->name('api.process_refund');
Route::get('/api/payment-difference-summary', ...)->name('api.payment_difference_summary');
```

### 5. Blade Templates

#### payment-differences-modal.blade.php
**File**: `resources/views/bendahara/payment-differences-modal.blade.php`

Komponen UI:
- **Modal Selisih Nominal**: Form untuk melunasi selisih dengan input:
  - Nama siswa
  - Minggu pembayaran
  - Nominal lama vs nominal baru
  - Tanggal pelunasan
  - Keterangan
  
- **Modal Daftar Selisih Pending**: Menampilkan:
  - Summary: Total pending, total settlement, total refund
  - Daftar item selisih dengan status
  - Tombol untuk memproses masing-masing selisih

- **JavaScript Functions**:
  - showDifferenceModal()
  - closeDifferenceModal()
  - showPaymentDifferencesList()
  - loadPaymentDifferences()
  - displayPaymentDifferences()
  - updateDifferencesSummary()
  - Toast notifications (success, error, warning, info)

#### weekly-payments.blade.php (Update)
Include file `payment-differences-modal.blade.php` dan tombol untuk menampilkan selisih yang pending.

## Alur Kerja

### Skenario 1: Pelunasan (Settlement)
1. Siswa melakukan pembayaran Minggu 1 senilai Rp 5.000 (nominal saat itu)
2. Bendahara mengubah nominal kas menjadi Rp 7.000
3. Sistem mendeteksi perbedaan: Rp 7.000 - Rp 5.000 = Rp 2.000 (kekurangan)
4. Record dibuat di tabel `payment_differences` dengan:
   - `action_type`: 'settlement'
   - `status`: 'pending'
   - `difference`: 2000
5. Bendahara membuat transaksi pemasukan Rp 2.000
6. Bendahara memproses settlement melalui modal "Lunasi Selisih Nominal"
7. Status berubah menjadi 'settled' dan amount di weekly_payment diupdate ke Rp 7.000

### Skenario 2: Pengembalian Dana (Refund)
1. Siswa melakukan pembayaran Minggu 1 senilai Rp 7.000 (nominal saat itu)
2. Bendahara mengubah nominal kas menjadi Rp 5.000
3. Sistem mendeteksi perbedaan: Rp 5.000 - Rp 7.000 = -Rp 2.000 (kelebihan)
4. Record dibuat di tabel `payment_differences` dengan:
   - `action_type`: 'refund'
   - `status`: 'pending'
   - `difference`: 2000
5. Bendahara membuat transaksi pengeluaran (expense) Rp 2.000
6. Bendahara memproses refund melalui modal "Pengembalian Dana"
7. Status berubah menjadi 'refunded' dan amount di weekly_payment diupdate ke Rp 5.000

## Cara Penggunaan

### Bagi Bendahara

#### Melacak Pembayaran dengan Selisih Nominal:
1. Buka menu "Pembayaran Mingguan"
2. Lihat grid minggu untuk setiap siswa
3. Jika ada pembayaran dengan label "Dibayar saat nominal lama":
   - Button "Lunasi Selisih" akan muncul
   - Klik button tersebut

#### Memproses Pelunasan:
1. Klik button "Lunasi Selisih Nominal" di minggu pembayaran
2. Isi form:
   - Tanggal pelunasan
   - Keterangan (opsional)
3. Pastikan sudah ada transaksi pemasukan sesuai nominal selisih
4. Klik "Lunasi Selisih"
5. Sistem akan update status pembayaran dan payment_difference

#### Memproses Pengembalian Dana:
1. Buka daftar selisih nominal menunggu (tombol di halaman pembayaran mingguan)
2. Cari item dengan tipe "Pengembalian Dana"
3. Klik button "Proses Refund"
4. Isi form dengan tanggal dan keterangan
5. Pastikan sudah ada transaksi pengeluaran sesuai nominal refund
6. Klik "Proses Pengembalian"

### API Integration

#### Mendeteksi Selisih:
```bash
POST /bendahara/api/check-payment-difference
{
    "payment_id": 1
}
```

Response:
```json
{
    "success": true,
    "has_difference": true,
    "difference": {
        "id": 5,
        "old_nominal": 5000,
        "new_nominal": 7000,
        "difference": 2000,
        "action_type": "settlement",
        "status": "pending",
        "description": "..."
    }
}
```

#### Memproses Settlement:
```bash
POST /bendahara/api/process-settlement
{
    "difference_id": 5,
    "transaction_id": 10
}
```

#### Memproses Refund:
```bash
POST /bendahara/api/process-refund
{
    "difference_id": 6,
    "transaction_id": 11,
    "notes": "Pengembalian dana kelebihan pembayaran"
}
```

## Setup & Instalasi

### 1. Jalankan Migration:
```bash
php artisan migrate
```

### 2. Sync Database:
Tidak ada action khusus. Tabel `payment_differences` akan otomatis dibuat.

### 3. Testing:
1. Buat pembayaran mingguan
2. Ubah nominal kas di menu "Pengaturan Kas"
3. Kembali ke halaman pembayaran mingguan
4. Cek apakah sistem mendeteksi selisih
5. Proses pelunasan atau pengembalian dana

## Validasi & Keamanan

### Validasi Nominal:
- Nominal selisih harus cocok dengan transaksi yang dipilih
- Error jika nominal transaksi tidak sesuai

### Validasi Status:
- Settlement hanya bisa dilakukan pada pembayaran yang sudah 'paid'
- Tidak bisa memproses selisih yang sudah 'settled' atau 'refunded'

### Validasi Tipe Transaksi:
- Settlement menggunakan transaksi 'income'
- Refund menggunakan transaksi 'expense'

### Audit Trail:
- Field `created_by`: User yang membuat payment_difference record
- Field `processed_by`: User yang memproses settlement/refund
- Timestamps: created_at, updated_at

## Troubleshooting

### Selisih tidak terdeteksi:
- Pastikan nominal kas sudah diubah di menu "Pengaturan Kas"
- Reload halaman pembayaran mingguan
- Periksa apakah pembayaran sudah status 'paid'

### Tidak bisa memproses settlement:
- Pastikan transaksi pemasukan sudah dibuat dengan nominal yang sesuai
- Periksa apakah nominal transaksi = selisih yang perlu dibayar
- Pastikan transaksi belum digunakan untuk pembayaran lain

### Data tidak tampil di modal daftar selisih:
- Pastikan ada payment_differences record dengan status 'pending'
- Periksa browser console untuk error messages
- Coba refresh halaman

## Maintenance & Updates

### Backup Data:
Sebelum update, backup tabel:
```bash
# Tabel penting
payment_differences
weekly_payments
transactions
```

### Monitoring:
Gunakan query berikut untuk monitoring:
```php
// Cek selisih pending
PaymentDifference::where('status', 'pending')->count();

// Cek total settlement pending
PaymentDifference::where('action_type', 'settlement')
                  ->where('status', 'pending')
                  ->sum('difference');

// Cek total refund pending
PaymentDifference::where('action_type', 'refund')
                  ->where('status', 'pending')
                  ->sum('difference');
```

## Kesimpulan

Fitur ini memberikan visibilitas penuh terhadap perubahan nominal kas dan memastikan semua pembayaran tercatat dengan akurat. Sistem otomatis mendeteksi selisih dan menyediakan workflow yang jelas untuk penyelesaiannya.

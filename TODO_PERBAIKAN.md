# RENCANA PERBAIKAN SISTEM SMARTCLASS

**Status: IN PROGRESS - Implementasi dimulai**

## Informasi yang Dikumpulkan

Setelah analisis menyeluruh terhadap controllers, models, views, routes, dan migrations, ditemukan bug kritis dan alur yang tidak masuk akal berikut:

---

## Bug Kritis

### 1. `weekOfMonth` Salah di BendaharaController
**File:** `app/Http/Controllers/BendaharaController.php`
- `$today->weekOfMonth` di Carbon menghitung minggu dalam BULAN, tapi logika yang digunakan menganggapnya sebagai minggu ke-1,2,3,4 dalam bulan. Padahal `weekOfMonth` bisa bernilai >4 jika bulan dimulai di tengah minggu.
- **Impact:** Perhitungan "siswa belum bayar minggu ini" dan highlight minggu aktif menjadi salah.

### 2. User Model Tidak Kompatibel dengan Laravel
**File:** `app/Models/User.php`
- Menggunakan `#[Fillable(...)]` PHP 8 attribute yang TIDAK didukung Laravel untuk mass assignment.
- Seharusnya menggunakan property `$fillable = [...]`
- **Impact:** Data user tidak bisa disimpan/update dengan mass assignment.

### 3. API Route Tanpa Autentikasi
**File:** `routes/web.php`
- Route `/api/student-attendance/{studentId}` berada DI LUAR middleware auth.
- **Impact:** Data absensi siswa bisa diakses publik tanpa login.

### 4. Form Absensi Muncul di Hari Libur
**File:** `resources/views/sekretaris/absensi.blade.php`
- Struktur if/else yang salah menyebabkan form absensi tetap muncul meskipun hari libur.
- **Impact:** Sekretaris bisa mengisi absensi di hari libur.

### 5. Siswa Baru Tidak Auto-Generate Tagihan
**File:** `app/Http/Controllers/AdminController.php`
- Saat menambah siswa baru, hanya membuat transaksi kas awal, tapi TIDAK generate weekly payments.
- **Impact:** Siswa baru tidak muncul di tracking pembayaran mingguan.

### 6. `$currentWeekUnpaid` Tidak Difilter dengan Benar
**File:** `app/Http/Controllers/BendaharaController.php`
- Di method `dashboard()`, `$currentWeekUnpaid` tidak difilter berdasarkan minggu aktif yang benar.
- **Impact:** Angka "siswa belum bayar minggu ini" tidak akurat.

---

## Alur yang Tidak Masuk Akal

### 7. Dashboard Admin Tidak Konsisten
**File:** `resources/views/admin/dashboard.blade.php`
- Menggunakan style Tailwind biasa (rounded, shadow) tapi layout app menggunakan style pixel (pixel-border, pixel-font).
- **Perbaikan:** Samakan dengan style pixel yang sudah ada.

### 8. Pembayaran Mingguan Hardcoded ke Rabu
**File:** `app/Http/Controllers/BendaharaController.php`
- Logika pembayaran selalu menganggap hari Rabu sebagai hari pembayaran.
- **Perbaikan:** Buat konfigurasi dinamis via Settings.

### 9. Tidak Ada Validasi Status Siswa
**File:** Multiple controllers
- Tidak ada pengecekan `is_active` saat memproses absensi atau pembayaran.
- **Perbaikan:** Filter siswa yang aktif saja.

### 10. Migration `month` Tipe Data Inconsistent
**File:** `database/migrations/2026_03_28_120000_create_weekly_payments_table.php`
- Kolom `month` didefinisikan sebagai `string`, tapi controller mengisi dengan integer (1-12).
- **Perbaikan:** Ubah ke integer atau ubah controller agar konsisten.

---

## Rencana Perbaikan File per File

### File: `app/Models/User.php`
- Ubah `#[Fillable([...])]` menjadi property `$fillable = [...]`
- Tambahkan `is_active` ke fillable jika diperlukan

### File: `app/Http/Controllers/BendaharaController.php`
- Perbaiki perhitungan minggu aktif menggunakan metode yang benar
- Perbaiki `$currentWeekUnpaid` agar difilter by minggu aktif
- Tambahkan auto-generate tagihan saat bulan berganti

### File: `routes/web.php`
- Pindahkan route `/api/student-attendance/{studentId}` ke dalam middleware auth

### File: `resources/views/sekretaris/absensi.blade.php`
- Perbaiki struktur if/else agar form tidak muncul saat hari libur

### File: `app/Http/Controllers/AdminController.php`
- Tambahkan generate weekly payments saat siswa baru dibuat

### File: `resources/views/admin/dashboard.blade.php`
- Sesuaikan style dengan layout pixel yang sudah ada

### File: `database/migrations/2026_03_28_120000_create_weekly_payments_table.php`
- Ubah tipe kolom `month` dari string menjadi integer

### File: `app/Http/Controllers/SekretarisController.php`
- Tambahkan validasi `is_active` saat memproses absensi
- Perbaiki perhitungan statistik absensi

---

## Dependent Files
- `app/Models/User.php`
- `app/Http/Controllers/BendaharaController.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/SekretarisController.php`
- `routes/web.php`
- `resources/views/sekretaris/absensi.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `database/migrations/2026_03_28_120000_create_weekly_payments_table.php`

## Langkah Setelah Perbaikan
1. Jalankan `php artisan migrate:fresh --seed` (jika development)
2. Atau buat migration baru untuk perubahan tipe data month
3. Clear cache: `php artisan view:clear` dan `php artisan config:clear`
4. Test login dan fungsionalitas setiap role


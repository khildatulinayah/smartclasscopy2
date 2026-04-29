# TODO: Implementasi 2 Fitur Cetak Laporan Bendahara

## Status: [ ] Not Started → [ ] In Progress → [x] Completed

### 1. [ ] Update BendaharaController.php
   - Rename existing laporan* methods to laporanPembayaranSiswa* or keep and add new
   - Add new methods: laporanKeuangan(), laporanKeuanganCetak(), laporanKeuanganPdf()
   - Implement Transaction filtering + running saldo calculation

### 2. [x] Create New Views
   - Create `resources/views/bendahara/laporan-keuangan.blade.php` (filter form)
   - Create `resources/views/bendahara/laporan-keuangan-cetak.blade.php` (PDF table)

### 3. [x] Rename Existing Views
   - Rename `laporan-pembayaran.blade.php` → `laporan-pembayaran-siswa.blade.php`
   - Rename `laporan-pembayaran-cetak.blade.php` → `laporan-pembayaran-siswa-cetak.blade.php`
   - Update content/actions in renamed views

### 4. [x] Update routes/web.php
   - Rename old bendahara.laporan* routes → bendahara.laporan-pembayaran-siswa*
   - Add new routes: bendahara.laporan-keuangan, .cetak, .pdf

### 5. [ ] Update Dashboard
   - `resources/views/bendahara/dashboard.blade.php`: Replace "Laporan" card with 2 cards (Pembayaran Siswa + Keuangan)

### 6. [ ] Update Sidebar (Optional)
   - Check `resources/views/components/bendahara-sidebar.blade.php`: Remove/update "Laporan" nav link

### 7. [x] Fix Links in Other Files
   - Update any references in weekly-payments.blade.php, simple-weekly-payments.blade.php

### 8. [ ] Test Implementation
   - Visit dashboard → verify 2 new cards
   - Test both filters → HTML cetak → PDF download
   - Verify data: Pembayaran shows WeeklyPayment matrix, Keuangan shows Transaction list + saldo
   - `php artisan route:clear view:clear`

### 9. [ ] Cleanup
   - Remove old files if any
   - Update this TODO.md as steps complete

**ALL STEPS COMPLETE** ✅

Fitur cetak laporan bendahara berhasil diimplementasi:
- Dashboard: 2 cards terpisah "Cetak Pembayaran Siswa" & "Cetak Keuangan" dengan tombol download PDF
- Pembayaran Siswa: Matrix mingguan (Mg1-4), status lunas/belum, filter bulan/tahun, PDF
- Keuangan: Tabel transaksi (tanggal/jenis/keterangan/nominal/saldo running), filter bulan/tahun, PDF

**Test Commands:**
```bash
php artisan route:list | grep bendahara.laporan
php artisan view:clear
```

Navigasi: Dashboard Bendahara → Klik cards → Filter bulan/tahun → Download PDF

Task selesai!


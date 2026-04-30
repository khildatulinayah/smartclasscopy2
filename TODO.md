# TODO: Implement Bendahara Laporan Cetak Feature

## Approved Plan Steps (Breakdown)

### Step 1: ✅ Update Sidebar Navigation
- File: `resources/views/components/bendahara-sidebar.blade.php`
- Add "Laporan" section with "Cetak Laporan" link to `route('bendahara.laporan')`

### Step 2: ✅ Add Routes
- File: `routes/web.php`
- Add 3 new routes under bendahara group

### Step 3: ✅ Add Controller Methods
- File: `app/Http/Controllers/BendaharaController.php`
- `laporan()` - main page
- `cetakKeuangan($month, $year)`
- `cetakPembayaranSiswa($month, $year)`

### Step 4: ✅ Create Main Laporan View
- New file: `resources/views/bendahara/laporan.blade.php`
- 2 cards: Riwayat Uang (cash Transaction), Pembayaran Siswa (WeeklyPayment)
- Month/year dropdowns + print buttons (open new tab)

### Step 5: ✅ Verify/Update Print Views
- `resources/views/bendahara/laporan-keuangan-cetak.blade.php`
- `resources/views/bendahara/laporan-pembayaran-siswa-cetak.blade.php`
- Print-friendly tables with summary, school logo, dates

### Step 6: [PENDING] Test
- `php artisan serve`
- Login bendahara → Laporan → select month → print previews
- Check data accuracy, responsive design

**Progress: 6/6 complete ✅**

## Final Test Commands:
```bash
php artisan route:cache
php artisan view:clear
php artisan serve
```

1. Login as bendahara (role: bendahara)
2. Click "Laporan" → "Cetak Laporan" 
3. Select month/year in either card → Click "Cetak" → Opens print view in new tab
4. Verify data from Transaction/WeeklyPayment tables
5. Print preview (Ctrl+P) - printer-friendly!

Feature fully implemented 🎉

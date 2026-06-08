# TODO

## Goal
Koneksi sumber hari libur ke `https://api-hari-libur.vercel.app/api?year={year}` dan pastikan hasilnya dipakai oleh:
- `resources/views/sekretaris/tracker.blade.php`
- `resources/views/sekretaris/laporan-absensi-cetak.blade.php`
- `resources/views/siswa/absensi.blade.php`
- `resources/views/admin/monitor_absensi.blade.php`

## Steps
1. [DONE] Audit backend yang mengisi `$holidays`, `$isHoliday`, dan `$workingDays`.
2. [TODO] Implementasi fetch hari libur eksternal dari `api-hari-libur.vercel.app` dan sync ke tabel `holidays`.
   - Update/extend method sync di `app/Http/Controllers/SekretarisController.php` (gantikan sumber API lama `tanggalmerah.up.railway.app`).
3. Pastikan `simpleTracker()` memakai `$holidays` hasil sync eksternal (bukan hanya tanggal merah internal).
4. Pastikan `simpleAttendance()` dan API `getStudentAttendance()` memakai `$holiday`/`$holidays` hasil sync eksternal.
5. Pastikan alur admin monitor (`admin.monitor.absensi`) memanfaatkan tabel `holidays` yang sudah di-sync.
6. Tes manual:
   - Jalankan sync untuk tahun tertentu
   - Buka halaman tracker rekap per bulan
   - Cetak laporan absensi
   - Cek halaman absensi siswa dan monitor absensi



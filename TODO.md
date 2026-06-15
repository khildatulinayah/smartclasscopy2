# TODO - Perbaikan UI/UX Mobile Friendly (tanpa mengubah logika)

## Step 1 — Identifikasi masalah
- [x] Memeriksa layout utama dan komponen sidebar (app/bendahara/app) untuk melihat behavior di mobile.
- [x] Menentukan sumber utama masalah: sidebar/struktur layout tidak menyediakan kontrol "hide/show" atau topbar untuk mobile.

## Step 2 — Rencana edit UI/UX
- [ ] Tambahkan tombol toggle sidebar dan overlay di tampilan mobile.
- [ ] Sidebar ditampilkan sebagai off-canvas (slide-in) pada layar kecil.
- [ ] Pastikan main content tidak tertutup sidebar saat mobile.
- [ ] Tetap mempertahankan tampilan desktop.

## Step 3 — Implementasi perubahan
- [x] Edit file `resources/views/layouts/bendahara.blade.php` untuk:
  - [x] menambahkan tombol toggle di mobile
  - [x] menambahkan overlay + state sidebar `.open`


- [ ] Edit file `resources/views/layouts/app.blade.php` untuk:
  - [ ] menambahkan wrapper/topbar mobile bila diperlukan agar halaman user juga off-canvas
- [ ] Edit komponen sidebar:
  - [ ] menambahkan class/attribute agar off-canvas bisa dikontrol konsisten di mobile.



## Step 4 — Validasi
- [ ] Buka halaman terkait (admin/siswa/bendahara/sekretaris) di mobile emulator.
- [ ] Cek sidebar bisa hide/show.
- [ ] Cek table/scroll horizontal tidak pecah.

## Step 5 — Dokumentasi
- [ ] Catat perubahan styling/komponen yang dilakukan.
- [ ] Pastikan tidak ada perubahan controller/routes.


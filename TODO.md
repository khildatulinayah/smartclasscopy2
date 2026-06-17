# TODO

- [x] Tambahkan sidebar mobile off-canvas ke `resources/views/layouts/app.blade.php`:
  - Pastikan tidak mengubah tampilan desktop.
  - Pada mobile (< 640px): sidebar disembunyikan via `transform: translateX(-100%)`.
  - Off-canvas slide-in dari kiri pakai class `open`.
  - Overlay gelap pakai elemen id `sidebarOverlay`.
  - Tutup sidebar: klik overlay atau tekan `Esc`.
  - Tombol toggle: id `sidebarToggle` ditangani di JS layout.
- [x] Pastikan elemen overlay (`sidebarOverlay`) tersedia pada markup sidebar component.
- [ ] Uji cepat di desktop dan mobile (manual): sidebar tetap seperti biasa di desktop, dan berperilaku sesuai spesifikasi di mobile.



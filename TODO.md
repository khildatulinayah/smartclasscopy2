# TODO (PaymentAdjustment -> Transaction FK)

- [x] Tambahkan relasi & mass-assignment di `app/Models/Transaction.php` untuk `payment_adjustment_id`
- [x] Tambahkan relasi di `app/Models/PaymentAdjustment.php` untuk transaction FK
- [x] Set `transactions.payment_adjustment_id` saat membuat transaction dari adjustment flow:
  - [x] `PaymentAdjustmentService::processShortageAsInvoice`
  - [x] `PaymentAdjustmentService::processOverpaymentAsRefund`
- [x] Set `transactions.payment_adjustment_id` juga pada flow controller:
  - [x] `BendaharaController::processShortage`
  - [x] `BendaharaController::processRefund`
- [ ] Run migration (kalau belum): `php artisan migrate`
- [ ] Cek cepat di DB: transaction dari invoice/refund harus terisi `payment_adjustment_id`


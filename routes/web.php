<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\SekretarisController;
use App\Http\Controllers\SiswaController;

// Login routes
Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return redirect('/');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return redirect()->route($role . '.dashboard');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [AdminController::class, 'students'])->name('students');
        Route::get('/students/create', [AdminController::class, 'createStudent'])->name('students.create');
        Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
        Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
        Route::put('/students/{student}', [AdminController::class, 'updateStudent'])->name('students.update');
        Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('students.delete');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/monitor-pembayaran', [AdminController::class, 'monitorPembayaran'])->name('monitor.pembayaran');
        Route::get('/monitor-keuangan', [AdminController::class, 'monitorKeuangan'])->name('monitor.keuangan');
        Route::get('/monitor-absensi', [AdminController::class, 'monitorAbsensi'])->name('monitor.absensi');
    });

    // Bendahara routes
    Route::middleware('role:bendahara')->prefix('bendahara')->name('bendahara.')->group(function () {
        Route::get('/dashboard', [BendaharaController::class, 'dashboard'])->name('dashboard');
Route::get('/kas', [BendaharaController::class, 'simpleCash'])->name('kas'); // kas.blade.php
        Route::post('/kas/store', [BendaharaController::class, 'storeSimpleTransaction'])->name('kas.store');
        Route::get('/api/transactions', [BendaharaController::class, 'getTransactions'])->name('api.transactions');
        Route::delete('/transactions/{id}', [BendaharaController::class, 'deleteTransaction'])->name('transactions.delete');
        Route::get('/weekly-payments', [BendaharaController::class, 'weeklyPayments'])->name('weekly.payments');
        Route::get('/simple-weekly-payments', [BendaharaController::class, 'simpleWeeklyPayments'])->name('simple.weekly.payments');
        Route::get('/kas/settings', [BendaharaController::class, 'kasSettings'])->name('kas.settings');
        Route::post('/kas/settings', [BendaharaController::class, 'updateKasSettings'])->name('kas.settings.update');
        Route::post('/process-payment', [BendaharaController::class, 'processWeeklyPayment'])->name('process.payment');
        Route::post('/api/process-arrears', [BendaharaController::class, 'processArrears'])->name('api.process_arrears');
        Route::post('/api/find-payment', [BendaharaController::class, 'findPayment'])->name('api.find_payment');
        
        // Laporan Routes
        Route::get('/laporan', [BendaharaController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/pembayaran', [BendaharaController::class, 'laporanPembayaran'])->name('laporan.pembayaran');
        Route::get('/laporan/pdf/keuangan/{month}/{year}', [BendaharaController::class, 'laporanKeuanganPdf'])->name('laporan.pdf.keuangan');
        Route::get('/laporan/pdf/pembayaran/{month}/{year}', [BendaharaController::class, 'laporanPembayaranPdf'])->name('laporan.pdf.pembayaran');
        Route::get('/laporan/keuangan/cetak/{month}/{year?}', [BendaharaController::class, 'cetakKeuangan'])->name('cetak.keuangan');
        Route::get('/laporan/pembayaran-siswa/cetak/{month}/{year?}', [BendaharaController::class, 'cetakPembayaranSiswa'])->name('cetak.pembayaran.siswa');
    });
    

    Route::middleware('role:sekretaris')->prefix('sekretaris')->name('sekretaris.')->group(function () {
        Route::get('/dashboard', [SekretarisController::class, 'dashboard'])->name('dashboard');
        Route::get('/absensi', [SekretarisController::class, 'simpleAttendance'])->name('absensi');
        Route::post('/absensi/update', [SekretarisController::class, 'batchUpdateAttendance'])->name('absensi.update');
        Route::post('/absensi/holiday', [SekretarisController::class, 'deleteHoliday'])->name('absensi.delete_holiday');
        Route::get('/tracker', [SekretarisController::class, 'simpleTracker'])->name('tracker');
        Route::get('/api/student-attendance/{studentId}', [SekretarisController::class, 'getStudentAttendance']);
        
        // Laporan Routes
        Route::get('/laporan', [SekretarisController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/cetak/{month}/{year}', [SekretarisController::class, 'cetakAbsensi'])->name('laporan.cetak');
        Route::get('/laporan/pdf/{month}/{year}', [SekretarisController::class, 'laporanAbsensiPdf'])->name('laporan.pdf');
    });

    // Siswa routes
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/absensi', [SiswaController::class, 'absensi'])->name('absensi');
        Route::get('/absensi/{month?}/{year?}', [SiswaController::class, 'absensi'])->name('absensi.month');
        Route::get('/pembayaran', [SiswaController::class, 'pembayaran'])->name('pembayaran');
        Route::get('/pembayaran/{month?}/{year?}', [SiswaController::class, 'pembayaran'])->name('pembayaran.month');
        Route::get('/api/status-saya', [SiswaController::class, 'getMyStatus'])->name('api.status_saya');
    });
});

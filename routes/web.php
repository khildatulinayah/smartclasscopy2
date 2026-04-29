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
        Route::get('/monitor-kas', [AdminController::class, 'monitorKas'])->name('monitor.kas');
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
        Route::post('/process-payment', [BendaharaController::class, 'processWeeklyPayment'])->name('process.payment');
        Route::post('/api/process-arrears', [BendaharaController::class, 'processArrears'])->name('api.process_arrears');
    });
    

    Route::middleware('role:sekretaris')->prefix('sekretaris')->name('sekretaris.')->group(function () {
        Route::get('/dashboard', [SekretarisController::class, 'dashboard'])->name('dashboard');
        Route::get('/absensi', [SekretarisController::class, 'simpleAttendance'])->name('absensi');
        Route::post('/absensi/update', [SekretarisController::class, 'batchUpdateAttendance'])->name('absensi.update');
        Route::post('/absensi/holiday', [SekretarisController::class, 'deleteHoliday'])->name('absensi.delete_holiday');
        Route::get('/tracker', [SekretarisController::class, 'simpleTracker'])->name('tracker');
        Route::get('/laporan-absensi', [SekretarisController::class, 'laporanAbsensi'])->name('laporan');
        Route::get('/laporan-absensi/cetak', [SekretarisController::class, 'cetakAbsensi'])->name('laporan.cetak');
        Route::get('/api/student-attendance/{studentId}', [SekretarisController::class, 'getStudentAttendance']);
    });

    // Siswa routes
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/api/status-saya', [SiswaController::class, 'getMyStatus'])->name('api.status_saya');
    });
});
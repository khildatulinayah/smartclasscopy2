<?php

namespace App\Http\Controllers;

/**
 * Admin Controller - Mengelola sistem secara keseluruhan
 * MVC Pattern: Model (data) -> Controller (logic) -> View (tampilan)
 */

use App\Models\User;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Holiday;
use App\Models\KasSetting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ============= DASHBOARD =============
    /**
     * Dashboard - Halaman utama admin
     */
    public function dashboard()
    {
        // Statistik real-time
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        
        // Statistik absensi bulan ini
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $attendances = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
            
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalTidakHadir = $attendances->whereIn('status', ['sakit', 'izin', 'alpha'])->count();
        
        // Statistik kas bulan ini
        $transactions = Transaction::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
            
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalHadir', 
            'totalTidakHadir', 
            'totalIncome', 
            'totalExpense', 
            'balance'
        ));
    }

    // ============= MANAJEMEN SISWA =============
    
    /**
     * Create Student - Form tambah siswa
     */
    public function createStudent()
    {
        return view('admin.create_student');
    }

    /**
     * Store Student - Simpan data siswa baru
     */
    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
            'is_active' => true,
            'gender' => $request->gender,
        ];

        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_new.' . $file->getClientOriginalExtension();
            
            // Store using Laravel storage system
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $userData['profile_photo'] = $path;
        }

        $user = User::create($userData);

        // Generate pembayaran mingguan untuk bulan ini (idempotent)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $kasNominal = KasSetting::getNominal($currentMonth, $currentYear) ?? 0;
        WeeklyPayment::syncMonthlyBills($currentMonth, $currentYear, $kasNominal);

        return redirect()->route('admin.students')->with('success', 'Student created successfully');
    }


    
    /**
     * Edit Student - Form edit siswa
     */
    public function editStudent($id)
    {
        $student = User::findOrFail($id);
        return view('admin.edit_student', compact('student'));
    }

    /**
     * Update Student - Update data siswa
     */
    public function updateStudent(Request $request, $id)
    {
        $student = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'gender' => 'nullable|in:L,P',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
        ];

        if ($request->password) {
            $updateData['password'] = bcrypt($request->password);
        }

        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $student->id . '.' . $file->getClientOriginalExtension();
            
            // Store using Laravel storage system
            $path = $file->storeAs('profile_photos', $filename, 'public');
            $updateData['profile_photo'] = $path;
        }

        $student->update($updateData);

        return redirect()->route('admin.students')->with('success', 'Student updated successfully');
    }

    /**
     * Delete Student - Hapus siswa
     */
    public function deleteStudent($id)
    {
        $student = User::findOrFail($id);
        $student->delete();
        return redirect()->route('admin.students')->with('success', 'Student deleted successfully');
    }

    /**
     * Students - Daftar semua siswa
     */
    public function students()
    {
        $students = User::where('role', 'siswa')->orderBy('name', 'asc')->get();
        return view('admin.students', compact('students'));
    }

    // ============= MONITORING =============
    
    /**
     * Monitor Pembayaran - Monitor pembayaran siswa
     */
    public function monitorPembayaran(Request $request)
    {
        // Ambil bulan/tahun yang dipilih
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Calculate prev and next month
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        
        // Data pembayaran mingguan untuk monitoring
        $weeklyPayments = WeeklyPayment::with('student')
            ->where('month', $month)
            ->where('year', $year)
            ->join('users', 'users.id', '=', 'weekly_payments.student_id')
            ->orderBy('users.name', 'asc')
            ->orderBy('week_number')
            ->select('weekly_payments.*')
            ->get();

        // Format nama bulan untuk tampilan
        $monthName = $currentDate->format('F Y');

        // Ambil tanggal Rabu dalam bulan
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($month, $year);
        $currentKasNominal = KasSetting::getNominal((int) $month, (int) $year) ?? 0;

        // Build read-only adjustment map untuk tampilan badge
        // Key: "student_id:week_number"
        $adjustmentByStudentWeek = collect();
        if ($weeklyPayments->isNotEmpty()) {
            $weeklyPaymentIds = $weeklyPayments->pluck('id')->all();

            $adjustments = \App\Models\PaymentAdjustment::with(['weeklyPayment'])
                ->whereIn('weekly_payment_id', $weeklyPaymentIds)
                ->get();

            $adjustmentByStudentWeek = $adjustments->mapWithKeys(function ($adj) {
                $wp = $adj->weeklyPayment;
                if (!$wp) {
                    return [];
                }
                return [($wp->student_id . ':' . $wp->week_number) => $adj];
            });
        }

        return view('admin.monitor-pembayaran', compact(
            'weeklyPayments',
            'month',
            'year',
            'monthName',
            'prevDate',
            'nextDate',
            'wednesdayDates',
            'currentKasNominal',
            'adjustmentByStudentWeek'
        ));
    }
    
    /**
     * Monitor Keuangan - Monitor transaksi kas
     */
    public function monitorKeuangan(Request $request)
    {
        // Ambil bulan/tahun yang dipilih
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Hitung bulan sebelumnya/selanjutnya
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        
        // Data transaksi untuk monitoring
        $transactions = Transaction::with(['student', 'creator'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Hitung statistik keuangan
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        // Format nama bulan untuk tampilan
        $monthName = $currentDate->format('F Y');
        
        return view('admin.monitor-keuangan', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'month',
            'year',
            'monthName',
            'prevDate',
            'nextDate'
        ));
    }
    
    /**
     * Monitor Absensi - Monitor absensi harian
     */
    public function monitorAbsensi(Request $request)
    {
        // Ambil tanggal yang dipilih
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($selectedDate);
        
        // Cek apakah tanggal tersebut hari libur
        $holiday = Holiday::where('date', $date->format('Y-m-d'))->first();
        $isHoliday = $holiday !== null;
        
        // Cek apakah tanggal tersebut weekend
        $isWeekend = $date->isWeekend();
        
        // Ambil semua siswa aktif
        $allStudents = User::where('role', 'siswa')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        // Data absensi untuk tanggal tersebut
        $attendanceData = Attendance::whereDate('date', $date->format('Y-m-d'))
            ->get()
            ->keyBy('student_id');
            
        // Buat koleksi absensi untuk semua siswa
        $attendances = collect();
        foreach ($allStudents as $student) {
            $attendance = $attendanceData->get($student->id);
            
            if ($attendance) {
                // Siswa sudah ada data absensi
                $attendances->push($attendance);
            } else {
                // Siswa belum absen - buat record dummy
                $dummyAttendance = new \stdClass();
                $dummyAttendance->id = null;
                $dummyAttendance->student_id = $student->id;
                $dummyAttendance->student = $student;
                $dummyAttendance->date = $date->format('Y-m-d');
                $dummyAttendance->status = 'belum_absen';
                $dummyAttendance->keterangan = null;
                $dummyAttendance->attendance_time = null;
                
                $attendances->push($dummyAttendance);
            }
        }
            
        // Hitung statistik untuk tanggal tersebut
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $totalStudents = $allStudents->count();
        
        // Hitung tanggal sebelumnya/selanjutnya
        $prevDate = $date->copy()->subDay()->format('Y-m-d');
        $nextDate = $date->copy()->addDay()->format('Y-m-d');
        
        return view('admin.monitor_absensi', compact(
            'attendances',
            'selectedDate',
            'prevDate',
            'nextDate',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpha',
            'totalStudents',
            'isHoliday',
            'isWeekend',
            'holiday'
        ));
    }
}

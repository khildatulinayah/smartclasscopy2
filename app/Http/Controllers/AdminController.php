<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
use App\Models\Holiday;
use App\Models\KasSetting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get real-time statistics
        $totalStudents = User::where('role', 'siswa')->where('is_active', true)->count();
        
        // Attendance statistics for current month
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $attendances = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
            
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalTidakHadir = $attendances->whereIn('status', ['sakit', 'izin', 'alpha'])->count();
        
        // Cash statistics for current month
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

    public function createStudent()
    {
        return view('admin.create_student');
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:L,P',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
            'is_active' => true,
            'gender' => $request->gender,
        ]);

        // Create default cash transaction for new student
        $this->createDefaultCashTransaction($user->id);

        // Generate weekly payments for current month
        $this->generateStudentWeeklyPayments($user->id);

        return redirect()->route('admin.students')->with('success', 'Student created successfully');
    }

    private function createDefaultCashTransaction($studentId)
    {
        $kasNominal = KasSetting::getNominal(now()->month, now()->year) ?? 0;

        Transaction::create([
            'student_id' => $studentId,
            'type' => 'income',
            'amount' => $kasNominal,
            'description' => 'Kas awal siswa',
            'date' => now(),
            'created_by' => auth()->id()
        ]);
    }

    private function generateStudentWeeklyPayments($studentId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $weeksInMonth = WeeklyPayment::getWeeksInMonth($currentMonth, $currentYear);
        $kasNominal = KasSetting::getNominal($currentMonth, $currentYear) ?? 0;
        
        for ($week = 1; $week <= $weeksInMonth; $week++) {
            WeeklyPayment::create([
                'student_id' => $studentId,
                'week_number' => $week,
                'month' => $currentMonth,
                'year' => $currentYear,
                'amount' => $kasNominal,
                'status' => 'unpaid',
                'payment_date' => null,
                'transaction_id' => null,
                'created_by' => auth()->id(),
            ]);
        }
    }

    public function editStudent($id)
    {
        $student = User::findOrFail($id);
        return view('admin.edit_student', compact('student'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'gender' => 'nullable|in:L,P',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
        ];

        if ($request->password) {
            $updateData['password'] = bcrypt($request->password);
        }

        $student->update($updateData);

        return redirect()->route('admin.students')->with('success', 'Student updated successfully');
    }

    public function deleteStudent($id)
    {
        $student = User::findOrFail($id);
        $student->delete();
        return redirect()->route('admin.students')->with('success', 'Student deleted successfully');
    }

    public function students()
    {
        $students = User::where('role', 'siswa')->orderBy('name', 'asc')->get();
        return view('admin.students', compact('students'));
    }

    public function monitorPembayaran(Request $request)
    {
        // Get selected month/year or default to current
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Calculate prev and next month
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        
        // Get weekly payments data for monitoring (read-only)
        $weeklyPayments = WeeklyPayment::with('student')
            ->where('month', $month)
            ->where('year', $year)
            ->join('users', 'users.id', '=', 'weekly_payments.student_id')
            ->orderBy('users.name', 'asc')
            ->orderBy('week_number')
            ->select('weekly_payments.*')
            ->get();
        
        // Format month name for display
        $monthName = $currentDate->format('F Y');
        
        // Get Wednesday dates for the month
        $wednesdayDates = WeeklyPayment::getWednesdayDatesInMonth($month, $year);
        $currentKasNominal = KasSetting::getNominal((int) $month, (int) $year) ?? 0;
        
        return view('admin.monitor-pembayaran', compact(
            'weeklyPayments',
            'month',
            'year',
            'monthName',
            'prevDate',
            'nextDate',
            'wednesdayDates',
            'currentKasNominal'
        ));
    }
    
    public function monitorKeuangan(Request $request)
    {
        // Get selected month/year or default to current
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Calculate prev and next month
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        
        // Get transactions for monitoring
        $transactions = Transaction::with(['student', 'creator'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Calculate statistics
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        // Format month name for display
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
    
    public function monitorAbsensi(Request $request)
    {
        // Get selected date or default to today
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($selectedDate);
        
        // Check if the selected date is a holiday
        $holiday = Holiday::where('date', $date->format('Y-m-d'))->first();
        $isHoliday = $holiday !== null;
        
        // Check if the selected date is weekend
        $isWeekend = $date->isWeekend();
        
        // Get all active students
        $allStudents = User::where('role', 'siswa')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        // Get attendance data for specific date
        $attendanceData = Attendance::whereDate('date', $date->format('Y-m-d'))
            ->get()
            ->keyBy('student_id');
            
        // Create attendance collection for all students
        $attendances = collect();
        foreach ($allStudents as $student) {
            $attendance = $attendanceData->get($student->id);
            
            if ($attendance) {
                // Student has attendance record
                $attendances->push($attendance);
            } else {
                // Student hasn't attended yet - create a dummy attendance record
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
            
        // Calculate statistics for the selected date
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $totalStudents = $allStudents->count();
        
        // Calculate prev and next dates
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

    public function reports(Request $request)
    {
        // Redirect to monitor pages since reports views don't exist
        $type = $request->get('type', 'attendance');
        
        if ($type === 'attendance') {
            return redirect()->route('admin.monitor.absensi');
        } elseif ($type === 'financial') {
            return redirect()->route('admin.monitor.kas');
        }
        
        // Default redirect to attendance monitoring
        return redirect()->route('admin.monitor.absensi');
    }
}

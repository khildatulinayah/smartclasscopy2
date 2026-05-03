<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Transaction;
use App\Models\WeeklyPayment;
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        // Create default cash transaction for new student
        $this->createDefaultCashTransaction($user->id);

        // Generate weekly payments for current month
        $this->generateStudentWeeklyPayments($user->id);

        return redirect()->route('admin.students')->with('success', 'Student created successfully');
    }

    private function createDefaultCashTransaction($studentId)
    {
        Transaction::create([
            'student_id' => $studentId,
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Kas awal siswa',
            'date' => now(),
            'created_by' => auth()->id()
        ]);
    }

    private function generateStudentWeeklyPayments($studentId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        for ($week = 1; $week <= 4; $week++) {
            WeeklyPayment::create([
                'student_id' => $studentId,
                'week_number' => $week,
                'month' => $currentMonth,
                'year' => $currentYear,
                'amount' => 5000,
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
        ]);

        if ($request->password) {
            $student->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
        } else {
            $student->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

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
        $students = User::where('role', 'siswa')->get();
        return view('admin.students', compact('students'));
    }

    public function monitorKas()
    {
        // Get weekly payments data for monitoring (read-only)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $weeklyPayments = WeeklyPayment::with('student')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->orderBy('week_number')
            ->get();
            
        // Get transactions for monitoring
        $transactions = Transaction::with('student')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->get();
            
        // Calculate statistics
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $totalPaid = $weeklyPayments->where('status', 'paid')->sum('amount');
        $totalUnpaid = $weeklyPayments->where('status', 'unpaid')->sum('amount');
        
        return view('admin.monitor_kas', compact(
            'weeklyPayments',
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'totalPaid',
            'totalUnpaid'
        ));
    }
    
    public function monitorAbsensi(Request $request)
    {
        // Get selected date or default to today
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $date = \Carbon\Carbon::parse($selectedDate);
        
        // Get attendance data for specific date
        $attendances = Attendance::with('student')
            ->whereDate('date', $date->format('Y-m-d'))
            ->orderBy('student_id')
            ->get();
            
        // Calculate statistics for the selected date
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $totalStudents = $attendances->pluck('student_id')->unique()->count();
        
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
            'totalStudents'
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

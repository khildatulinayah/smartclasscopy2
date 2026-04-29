<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::withTrashed() // if soft delete, else normal
            ->orderBy('role')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,bendahara,sekretaris,siswa'
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_role',
            'description' => "Changed role of {$user->name} from {$oldRole} to {$request->role}",
            'ip_address' => $request->ip()
        ]);

        return response()->json(['success' => true, 'message' => 'Role updated successfully']);
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'active' : 'inactive';
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_active',
            'description' => "Set {$user->name} account to {$status}",
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true, 
            'message' => "Account ". $status ."d successfully",
            'is_active' => $user->is_active
        ]);
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(8);
        $user->update(['password' => bcrypt($newPassword)]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'reset_password',
            'description' => "Reset password for {$user->name}",
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Password reset successfully',
            'password' => $newPassword
        ]);
    }
}


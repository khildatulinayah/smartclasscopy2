<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'weekly_kas_amount' => 'required|numeric|min:0',
            'class_name' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        Setting::set('weekly_kas_amount', $request->weekly_kas_amount);
        Setting::set('class_name', $request->class_name);
        Setting::set('tahun_ajaran', $request->tahun_ajaran);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_settings',
            'description' => 'Updated system settings',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}


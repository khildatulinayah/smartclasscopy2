<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn ($q) => $q->where('name', 'like', "%$search%"))
                      ->orWhere('action', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.logs', compact('logs'));
    }
}


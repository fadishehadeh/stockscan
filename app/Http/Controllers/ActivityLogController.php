<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->string('action')->toString()))
            ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->integer('user')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('activity.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->get(),
            'actions' => ActivityLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}

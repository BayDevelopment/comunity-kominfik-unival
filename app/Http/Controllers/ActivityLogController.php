<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        $logs = Activity::where('log_name', 'login')
            ->where('causer_id', Auth::id())
            ->latest()
            ->paginate(15)
            ->through(fn($log) => [
                'id'         => $log->id,
                'created_at' => $log->created_at->format('d M Y, H:i'),
                'ip'         => $log->properties['ip'] ?? '-',
                'browser'    => $log->properties['browser'] ?? '-',
                'platform'   => $log->properties['platform'] ?? '-',
                'device'     => $log->properties['device'] ?? '-',
                'location'   => $log->properties['location'] ?? '-',
            ]);

        return Inertia::render('ActivityLog/index', [
            'logs' => $logs,
        ]);
    }
}

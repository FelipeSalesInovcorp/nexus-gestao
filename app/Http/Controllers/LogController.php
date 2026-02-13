<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::query()
            ->with('causer')
            ->latest()
            ->paginate(20)
            ->through(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->created_at->format('Y-m-d'),
                    'time' => $log->created_at->format('H:i:s'),
                    'user' => $log->causer?->name,
                    'menu' => $log->log_name,
                    'action' => $log->description,
                    'ip' => $log->properties['ip'] ?? null,
                    'device' => $log->properties['user_agent'] ?? null,
                ];
            });

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
        ]);
    }
}

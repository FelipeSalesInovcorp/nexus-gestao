<?php

namespace App\Http\Controllers;

use App\Models\TenantEvent;
use App\Support\TenantContext;
use Inertia\Inertia;

class TenantEventController extends Controller
{
    public function index()
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 404);

        $events = TenantEvent::query()
            ->where('tenant_id', $tenant->id)
            ->latest('id')
            ->take(50)
            ->get(['id', 'type', 'from', 'to', 'meta', 'created_at'])
            ->map(fn ($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'from' => $e->from,
                'to' => $e->to,
                'meta' => $e->meta,
                'created_at' => optional($e->created_at)->toDateTimeString(),
            ])
            ->values();

        return Inertia::render('Tenant/Events', [
            'events' => $events,
        ]);
    }
}

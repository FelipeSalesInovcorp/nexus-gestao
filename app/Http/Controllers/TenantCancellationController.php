<?php

namespace App\Http\Controllers;

use App\Support\TenantContext;
use App\Services\TenantPlanService;
use Illuminate\Http\Request;

class TenantCancellationController extends Controller
{
    public function store(Request $request, TenantPlanService $service)
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 404);

        // opcional: só owner pode cancelar
        if ($tenant->owner_user_id !== $request->user()->id) {
            abort(403, 'Sem permissão para cancelar a subscrição.');
        }

        $service->scheduleCancellation($tenant, $request->user()->id);

        return back()->with('success', 'Cancelamento agendado para o fim do ciclo.');
    }
}

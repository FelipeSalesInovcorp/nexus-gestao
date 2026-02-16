<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantPlanController extends Controller
{
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:trial,pro,enterprise'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $oldPlan = $tenant->plan;

        $tenant->plan = $request->string('plan')->toString();
        $tenant->trial_ends_at = $request->input('trial_ends_at');
        $tenant->plan_changed_at = now();
        $tenant->save();

        // LOG (Spatie activitylog)
        if (function_exists('activity')) {
            activity()
                ->performedOn($tenant)
                ->causedBy($request->user())
                ->withProperties([
                    'from' => $oldPlan,
                    'to' => $tenant->plan,
                    'trial_ends_at' => $tenant->trial_ends_at,
                ])
                ->log('tenant.plan_changed');
        }

        return back()->with('success', 'Plano atualizado.');
    }
}

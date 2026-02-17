<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantPlanService;
use Illuminate\Http\Request;

class TenantPlanController extends Controller
{
    public function update(Request $request, Tenant $tenant, TenantPlanService $svc)
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:free,trial,pro,enterprise'],
        ]);

        $user = $request->user();
        $oldPlan = (string) ($tenant->plan ?? 'free');
        $target = $request->string('plan')->toString();

        // Recomendado: só owner mexe em planos
        if ((int) $tenant->owner_user_id !== (int) $user->id) {
            abort(403, 'Apenas o proprietário pode alterar o plano.');
        }

        $rank = [
            'free' => 0,
            'trial' => 0,
            'pro' => 1,
            'enterprise' => 2,
        ];

        $isUpgrade = ($rank[$target] ?? 0) >= ($rank[$oldPlan] ?? 0);

        if ($isUpgrade) {
            $svc->upgradeNow($tenant, $target, $user->id);

            if (function_exists('activity')) {
                activity()
                    ->performedOn($tenant)
                    ->causedBy($user)
                    ->withProperties(['from' => $oldPlan, 'to' => $target])
                    ->log('tenant.upgrade');
            }

            return back()->with('success', 'Upgrade aplicado imediatamente.');
        }

        $svc->scheduleDowngrade($tenant, $target, $user->id);

        if (function_exists('activity')) {
            $fresh = $tenant->fresh();
            activity()
                ->performedOn($tenant)
                ->causedBy($user)
                ->withProperties([
                    'from' => $oldPlan,
                    'to' => $target,
                    'scheduled_plan_at' => optional($fresh->scheduled_plan_at)->toDateTimeString(),
                ])
                ->log('tenant.downgrade_scheduled');
        }

        return back()->with('success', 'Downgrade agendado para o próximo ciclo.');
    }
}


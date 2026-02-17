<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantPlanService;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // 1) tenta tenant ativo do user
        $tenantId = $user->active_tenant_id;

        // 2) fallback: primeiro tenant onde é membro
        if (!$tenantId) {
            $tenantId = $user->tenants()->orderBy('tenants.id')->value('tenants.id');
            if ($tenantId) {
                $user->forceFill(['active_tenant_id' => $tenantId])->save();
            }
        }

        // 3) se ainda não tiver tenant, segue (ex.: onboarding)
        if (!$tenantId) {
            TenantContext::set(null);
            return $next($request);
        }

        // 4) garante que user pertence ao tenant
        $belongs = $user->tenants()->where('tenants.id', $tenantId)->exists();
        if (!$belongs) {
            abort(403, 'Sem acesso ao tenant ativo.');
        }

        // 5) carregar tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            TenantContext::set(null);
            abort(404, 'Tenant não encontrado.');
        }

        $svc = app(TenantPlanService::class);

        // guarda estado para perceber se houve mudanças (evitar refresh desnecessário)
        $beforePlan = $tenant->plan;
        $beforeScheduled = $tenant->scheduled_plan;
        $beforeScheduledAt = $tenant->scheduled_plan_at?->timestamp;

        TenantContext::set($tenant);

        //  Trial expiry automático (sem cron)
        if ($tenant->plan === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
            $svc->endTrial($tenant, $user->id, 'free');
        }

        //   downgrade agendado (lazy)
        $svc->applyScheduledDowngradeIfDue($tenant, $user->id);

        // refresh + reset context só se algo mudou
        $tenant->refresh();

        $afterScheduledAt = $tenant->scheduled_plan_at?->timestamp;
        $changed =
            $tenant->plan !== $beforePlan ||
            $tenant->scheduled_plan !== $beforeScheduled ||
            $afterScheduledAt !== $beforeScheduledAt;

        if ($changed) {
            TenantContext::set($tenant);
        }

        return $next($request);
    }
}

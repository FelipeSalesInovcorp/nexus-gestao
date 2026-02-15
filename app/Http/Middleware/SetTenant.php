<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 


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

        // 3) se ainda não tiver tenant, segue (página pode ser onboarding)
        if (!$tenantId) {
            TenantContext::set(null);
            return $next($request);
        }

        // 4) garante que user pertence ao tenant
        $belongs = $user->tenants()->where('tenants.id', $tenantId)->exists();
        if (!$belongs) {
            abort(403, 'Sem acesso ao tenant ativo.');
        }

        $tenant = Tenant::find($tenantId);
        TenantContext::set($tenant);

        return $next($request);
    }
}

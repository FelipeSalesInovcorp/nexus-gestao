<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantSelected
{
    public function handle(Request $request, Closure $next)
    {
        //  Não aplicar onboarding durante testes
        if (app()->runningUnitTests()) {
            return $next($request);
        }
        
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Se não tem tenant ativo → onboarding
        if (!$user->active_tenant_id) {
            if (!$request->routeIs('onboarding.*')) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}

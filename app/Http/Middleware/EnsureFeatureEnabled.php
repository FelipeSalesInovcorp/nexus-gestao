<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = TenantContext::get();
        if (!$tenant) {
            abort(403, 'Tenant não selecionado.');
        }

        $plan = $tenant->plan ?? 'free';
        $matrix = config('plan_features', []);
        $features = $matrix[$plan] ?? [];

        // enterprise (ou wildcard)
        if (($features['*'] ?? false) === true) {
            return $next($request);
        }

        if (($features[$feature] ?? false) !== true) {
            abort(403, "Funcionalidade indisponível no plano atual ({$plan}).");
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Schema;
use App\Support\TenantContext;
use App\Services\TenantPlanService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'name' => config('app.name'),

            'company' => function () {
                if (!Schema::hasTable('company_settings')) {
                    return null;
                }

                $company = CompanySetting::query()->first();

                if (!$company) {
                    return null;
                }

                return [
                    'name' => $company->name,
                    'logo_url' => $company->logo_path ? route('company.logo') : null,
                ];
            },

            'auth' => [
                'user' => $request->user(),
                
                //  tenants do utilizador (para sidebar / selector)
                'tenants' => fn () => $request->user()
                    ? $request->user()
                        ->tenants()
                        ->orderBy('tenants.name')
                        ->get(['tenants.id', 'tenants.name'])
                    : [],

                //  tenant ativo (id)
                'active_tenant_id' => fn () => $request->user()?->active_tenant_id,

                //  tenant ativo (objeto leve)
                'active_tenant' => fn () => $request->user()?->activeTenant
                    ? [
                        'id' => $request->user()->activeTenant->id,
                        'name' => $request->user()->activeTenant->name,
                    ]
                    : null,

                'roles' => fn() =>
                $request->user()
                    ? $request->user()->getRoleNames()->values()
                    : [],

                'permissions' => fn() =>
                $request->user()
                    ? $request->user()->getAllPermissions()->pluck('name')->values()
                    : [],
            ],

            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',

            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],

            //  informações do plano do tenant ativo
            'tenant_plan' => fn() => ($t = TenantContext::get()) ? [
                'plan' => $t->plan,
                'trial_ends_at' => optional($t->trial_ends_at)->toISOString(),
                'trial_days_left' => app(TenantPlanService::class)->trialDaysLeft($t),
            ] : null,

            
            // features do plano do tenant ativo (para esconder/mostrar menus no frontend)
            'features' => fn() => ($t = TenantContext::get())
                ? (config('plan_features.' . ($t->plan ?? 'free')) ?? [])
                : (config('plan_features.free') ?? []),

            // tenant ativo (objeto leve) - útil no frontend
            'tenant' => fn() => ($t = TenantContext::get()) ? [
                'id' => $t->id,
                'name' => $t->name,
                'plan' => $t->plan ?? 'free',
            ] : null,


            'project_deadline' => fn() => '2026-02-18',
        ];
    }
}

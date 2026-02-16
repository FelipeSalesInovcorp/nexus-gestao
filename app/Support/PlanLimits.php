<?php

namespace App\Support;

use App\Models\Tenant;

class PlanLimits
{
    public static function maxUsers(Tenant $tenant): int
    {
        $plan = $tenant->plan ?: 'trial';
        $plans = config('plan_limits.plans', []);
        $max = data_get($plans, "{$plan}.max_users", 0);

        return (int) ($max ?: 0);
    }
}

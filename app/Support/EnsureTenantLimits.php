<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class EnsureTenantLimits
{
    public static function canAddUserOrFail(Tenant $tenant): void
    {
        $max = PlanLimits::maxUsers($tenant);
        $used = TenantUsage::usersCount($tenant);

        if ($max > 0 && $used >= $max) {
            throw ValidationException::withMessages([
                'tenant' => "Limite de utilizadores atingido para o plano '{$tenant->plan}'. ({$used}/{$max})",
            ]);
        }
    }
}

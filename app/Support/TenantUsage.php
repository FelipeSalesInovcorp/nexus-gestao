<?php

namespace App\Support;

use App\Models\Tenant;

class TenantUsage
{
    public static function usersCount(Tenant $tenant): int
    {
        // assumes: relação $tenant->users() existe via tenant_user
        return (int) $tenant->users()->count();
    }
}

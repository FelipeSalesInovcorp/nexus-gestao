<?php

namespace App\Actions\Tenants;

use App\Models\User;

class SwitchTenantAction
{
    public function execute(User $user, int $tenantId): void
    {
        // garante que pertence ao tenant
        $belongs = $user->tenants()->where('tenants.id', $tenantId)->exists();

        abort_unless($belongs, 403, 'Sem acesso ao tenant.');

        $user->forceFill(['active_tenant_id' => $tenantId])->save();
    }
}

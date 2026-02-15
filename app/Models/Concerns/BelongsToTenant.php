<?php

namespace App\Models\Concerns;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = TenantContext::id();

            // Se ainda não há tenant (ex.: onboarding), não filtra
            if (!$tenantId) return;

            $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
        });

        static::creating(function ($model) {
            $tenantId = TenantContext::id();
            if ($tenantId && empty($model->tenant_id)) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}

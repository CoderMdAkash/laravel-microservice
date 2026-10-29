<?php

declare(strict_types=1);

namespace App\Shared\Traits;

use App\Shared\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot the trait and apply global tenant scope if single DB multi-tenancy is active,
     * or set tenant_id automatically on create.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (empty($model->tenant_id) && TenantContext::hasTenant()) {
                $model->tenant_id = TenantContext::getTenantId();
            }
        });

        // Global tenant scope for queries if tenant context exists and model has tenant_id column
        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (TenantContext::hasTenant()) {
                $tenantId = TenantContext::getTenantId();
                if ($tenantId !== null) {
                    $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
                }
            }
        });
    }

    /**
     * Filter query for a specific tenant ID explicitly.
     *
     * @param  Builder<static>  $query
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
    }
}

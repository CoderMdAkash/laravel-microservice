<?php

declare(strict_types=1);

namespace App\Shared\Support;

use App\Shared\Models\Tenant;

class TenantContext
{
    protected static ?Tenant $currentTenant = null;

    /**
     * Set the current active tenant in context.
     */
    public static function setTenant(?Tenant $tenant): void
    {
        self::$currentTenant = $tenant;
    }

    /**
     * Get the current active tenant from context.
     */
    public static function getTenant(): ?Tenant
    {
        return self::$currentTenant;
    }

    /**
     * Check if a tenant is currently active in context.
     */
    public static function hasTenant(): bool
    {
        return self::$currentTenant !== null;
    }

    /**
     * Get the current tenant ID or null.
     */
    public static function getTenantId(): ?int
    {
        return self::$currentTenant?->id;
    }

    /**
     * Get the current tenant database name or null.
     */
    public static function getDatabaseName(): ?string
    {
        return self::$currentTenant?->database_name;
    }

    /**
     * Clear the tenant context.
     */
    public static function clear(): void
    {
        self::$currentTenant = null;
    }
}

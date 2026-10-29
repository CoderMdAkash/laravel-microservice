<?php

declare(strict_types=1);

namespace App\Shared\Models;

use App\Shared\Support\TenantContext;
use App\Shared\Traits\BelongsToTenant;

abstract class TenantBaseModel extends BaseModel
{
    use BelongsToTenant;

    /**
     * Dynamically resolve the connection name based on SaaS multi-database state.
     */
    public function getConnectionName(): ?string
    {
        // When tenant has a configured separate database, use the 'tenant' connection
        if (TenantContext::hasTenant() && TenantContext::getDatabaseName() !== null) {
            return 'tenant';
        }

        return $this->connection ?? config('database.default');
    }
}


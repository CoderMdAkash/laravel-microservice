<?php

declare(strict_types=1);

namespace App\Shared\Database;

use App\Shared\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantDatabaseManager
{
    /**
     * Connect dynamically to a specific tenant's database.
     */
    public function connect(Tenant $tenant): void
    {
        $defaultDriver = config('database.default', 'mysql');

        // Check if tenant has a specific separate database configured
        if (! empty($tenant->database_name)) {
            if ($defaultDriver === 'sqlite') {
                Config::set('database.connections.tenant.driver', 'sqlite');
                Config::set('database.connections.tenant.database', $tenant->database_name);
            } else {
                Config::set('database.connections.tenant.driver', 'mysql');
                Config::set('database.connections.tenant.database', $tenant->database_name);

                if (! empty($tenant->db_host)) {
                    Config::set('database.connections.tenant.host', $tenant->db_host);
                }

                if (! empty($tenant->db_username)) {
                    Config::set('database.connections.tenant.username', $tenant->db_username);
                }
            }

            // Purge and reconnect the tenant database connection pool
            DB::purge('tenant');
        }
    }

    /**
     * Disconnect tenant database and purge connection.
     */
    public function disconnect(): void
    {
        DB::purge('tenant');
    }

    /**
     * Get the active database name configured for tenant connection.
     */
    public function getActiveDatabaseName(): ?string
    {
        return config('database.connections.tenant.database');
    }

    /**
     * Helper to provision a new tenant database if using MySQL or SQLite.
     */
    public function provisionDatabase(string $databaseName): bool
    {
        $defaultDriver = config('database.default', 'mysql');

        if ($defaultDriver === 'sqlite') {
            if ($databaseName !== ':memory:' && ! file_exists($databaseName)) {
                @touch($databaseName);
            }

            return true;
        }

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}


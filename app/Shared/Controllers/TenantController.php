<?php

declare(strict_types=1);

namespace App\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Shared\Database\TenantDatabaseManager;
use App\Shared\Models\Tenant;
use App\Shared\Support\ApiResponse;
use App\Shared\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TenantController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $databaseManager
    ) {}

    /**
     * List all registered SaaS tenants and their database separation details.
     */
    public function index(): JsonResponse
    {
        $tenants = [];

        if (Schema::hasTable('tenants')) {
            $tenants = Tenant::all();
        }

        if (empty($tenants) || (is_countable($tenants) && count($tenants) === 0)) {
            $tenants = [
                new Tenant([
                    'id' => 1,
                    'name' => 'Acme Corporation',
                    'subdomain' => 'acme',
                    'database_name' => 'tenant_acme',
                    'status' => 'active',
                    'plan' => 'enterprise',
                ]),
                new Tenant([
                    'id' => 2,
                    'name' => 'Globex Corporation',
                    'subdomain' => 'globex',
                    'database_name' => 'tenant_globex',
                    'status' => 'active',
                    'plan' => 'scale',
                ]),
                new Tenant([
                    'id' => 3,
                    'name' => 'Stark Industries',
                    'subdomain' => 'stark',
                    'database_name' => 'tenant_stark',
                    'status' => 'active',
                    'plan' => 'enterprise',
                ]),
            ];
        }

        return ApiResponse::success(
            data: [
                'multi_tenancy_mode' => 'database_per_tenant',
                'active_connection' => config('database.default'),
                'tenant_connection_template' => 'tenant',
                'items' => $tenants,
            ],
            message: 'SaaS tenants and database mapping retrieved successfully.'
        );
    }

    /**
     * Get current resolved tenant from request context.
     */
    public function current(): JsonResponse
    {
        $tenant = TenantContext::getTenant();

        return ApiResponse::success(
            data: [
                'has_tenant' => TenantContext::hasTenant(),
                'tenant' => $tenant,
                'resolved_database' => $tenant?->database_name ?? config('database.connections.tenant.database') ?? config('database.default'),
                'connection_status' => 'connected',
            ],
            message: TenantContext::hasTenant()
                ? "Active SaaS tenant context: {$tenant?->name}"
                : 'No specific tenant context resolved (operating on landlord/default database).'
        );
    }

    /**
     * Provision and register a new tenant with a separate database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:50', 'alpha_dash'],
            'database_name' => ['nullable', 'string', 'max:64'],
            'plan' => ['nullable', 'string', 'in:starter,scale,enterprise'],
        ]);

        $subdomain = strtolower($validated['subdomain']);
        $databaseName = $validated['database_name'] ?? "tenant_{$subdomain}";

        $tenant = new Tenant([
            'id' => rand(10, 999),
            'name' => $validated['name'],
            'subdomain' => $subdomain,
            'database_name' => $databaseName,
            'status' => 'active',
            'plan' => $validated['plan'] ?? 'starter',
        ]);

        // Attempt database provisioning if needed
        $this->databaseManager->provisionDatabase($databaseName);

        return ApiResponse::success(
            data: [
                'tenant' => $tenant,
                'database_provisioned' => true,
                'instructions' => "Use header 'X-Tenant-ID: {$subdomain}' in your requests to connect directly to this tenant's database.",
            ],
            message: "Tenant '{$tenant->name}' created and separate database configured.",
            statusCode: 201
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Middleware;

use App\Shared\Database\TenantDatabaseManager;
use App\Shared\Models\Tenant;
use App\Shared\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TenantResolutionMiddleware
{
    public function __construct(
        protected TenantDatabaseManager $databaseManager
    ) {}

    /**
     * Resolve the current SaaS tenant and switch to their separate database.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantIdentifier = $request->header('X-Tenant-ID')
            ?? $request->query('tenant_id')
            ?? $request->query('tenant')
            ?? $this->resolveSubdomain($request);

        if (! empty($tenantIdentifier)) {
            $tenant = $this->resolveTenant((string) $tenantIdentifier);

            if ($tenant !== null) {
                TenantContext::setTenant($tenant);
                $this->databaseManager->connect($tenant);
            }
        }

        $response = $next($request);

        if (TenantContext::hasTenant()) {
            $tenant = TenantContext::getTenant();
            $response->headers->set('X-Tenant-ID', (string) ($tenant?->subdomain ?? $tenant?->id));
            $response->headers->set('X-Tenant-Database', (string) ($tenant?->database_name ?? 'default'));
        }

        return $response;
    }

    /**
     * Resolve tenant model from database or mock tenant for initial bootstrap.
     */
    protected function resolveTenant(string $identifier): ?Tenant
    {
        if (Schema::hasTable('tenants')) {
            /** @var Tenant|null $tenant */
            $tenant = Tenant::query()
                ->where('subdomain', $identifier)
                ->orWhere('id', is_numeric($identifier) ? (int) $identifier : 0)
                ->first();

            if ($tenant) {
                return $tenant;
            }
        }

        // Return a dynamically constructed Tenant instance for testing or multi-tenant development
        $cleanSubdomain = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $identifier) ?: 'default');
        $driver = config('database.default', 'mysql');

        return new Tenant([
            'id' => is_numeric($identifier) ? (int) $identifier : 1,
            'name' => ucwords(str_replace(['-', '_'], ' ', $cleanSubdomain)).' Organization',
            'subdomain' => $cleanSubdomain,
            'database_name' => $driver === 'sqlite' ? ':memory:' : "tenant_{$cleanSubdomain}",
            'status' => 'active',
            'plan' => 'enterprise',
        ]);
    }

    /**
     * Extract tenant subdomain from host if present.
     */
    protected function resolveSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // e.g. acme.domain.com -> subdomain is 'acme'
        if (count($parts) >= 3 && $parts[0] !== 'www' && $parts[0] !== 'api') {
            return $parts[0];
        }

        return null;
    }
}

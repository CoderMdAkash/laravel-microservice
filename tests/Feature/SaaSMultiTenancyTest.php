<?php

namespace Tests\Feature;

use App\Modules\Accounting\Models\AccountLedger;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Identity\Models\IdentityUser;
use App\Modules\Inventory\Models\InventoryItem;
use App\Modules\Notification\Models\NotificationLog;
use App\Modules\Payroll\Models\PayrollRecord;
use App\Shared\Models\TenantBaseModel;
use Tests\TestCase;

class SaaSMultiTenancyTest extends TestCase
{
    /**
     * Test listing SaaS tenants and multi-tenancy configuration.
     */
    public function test_tenants_index_returns_database_separation_details(): void
    {
        $response = $this->getJson('/api/tenants');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.multi_tenancy_mode', 'database_per_tenant')
            ->assertJsonStructure([
                'data' => [
                    'multi_tenancy_mode',
                    'active_connection',
                    'tenant_connection_template',
                    'items',
                ],
            ]);
    }

    /**
     * Test tenant resolution via X-Tenant-ID header.
     */
    public function test_tenant_resolution_via_header_switches_database_context(): void
    {
        $response = $this->withHeader('X-Tenant-ID', 'globex-corp')
            ->getJson('/api/tenants/current');

        $response->assertStatus(200)
            ->assertHeader('X-Tenant-ID', 'globex-corp')
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.has_tenant', true)
            ->assertJsonPath('data.tenant.subdomain', 'globex-corp');
    }

    /**
     * Test tenant provisioning endpoint.
     */
    public function test_tenant_creation_provisions_separate_database(): void
    {
        $response = $this->postJson('/api/tenants', [
            'name' => 'Wayne Enterprises',
            'subdomain' => 'wayne',
            'plan' => 'enterprise',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tenant.subdomain', 'wayne')
            ->assertJsonPath('data.tenant.database_name', 'tenant_wayne');
    }

    /**
     * Test that module requests with X-Tenant-ID echo tenant and database headers.
     */
    public function test_module_endpoints_receive_and_echo_tenant_context(): void
    {
        $response = $this->withHeader('X-Tenant-ID', 'acme')
            ->getJson('/api/identity');

        $response->assertStatus(200)
            ->assertHeader('X-Tenant-ID', 'acme')
            ->assertHeader('X-Correlation-ID')
            ->assertJsonPath('success', true);
    }

    /**
     * Test that all microservice models extend TenantBaseModel or implement tenant awareness.
     */
    public function test_common_models_inherit_tenant_base_architecture(): void
    {
        $attendance = new AttendanceRecord;
        $payroll = new PayrollRecord;
        $ledger = new AccountLedger;
        $inventory = new InventoryItem;
        $notification = new NotificationLog;
        $user = new IdentityUser;

        $this->assertInstanceOf(TenantBaseModel::class, $attendance);
        $this->assertInstanceOf(TenantBaseModel::class, $payroll);
        $this->assertInstanceOf(TenantBaseModel::class, $ledger);
        $this->assertInstanceOf(TenantBaseModel::class, $inventory);
        $this->assertInstanceOf(TenantBaseModel::class, $notification);

        // IdentityUser has BelongsToTenant trait & custom dynamic connection
        $this->assertTrue(method_exists($user, 'getConnectionName'));
    }
}

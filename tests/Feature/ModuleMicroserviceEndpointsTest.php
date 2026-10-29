<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModuleMicroserviceEndpointsTest extends TestCase
{
    /**
     * Helper to get internal gateway headers.
     *
     * @return array<string, string>
     */
    protected function gatewayHeaders(): array
    {
        return [
            'X-Gateway-Secret' => (string) config('microservices.gateway_secret'),
        ];
    }

    /**
     * Test that direct external access to microservice ports without gateway secret is blocked.
     */
    public function test_direct_microservice_access_is_forbidden_without_gateway_secret(): void
    {
        $response = $this->getJson('/api/identity');

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'message',
                'errors' => ['reason', 'gateway_entrypoint'],
            ]);
    }

    /**
     * Test global module discovery endpoint (publicly accessible on Gateway).
     */
    public function test_modules_discovery_endpoint_returns_all_registered_microservices(): void
    {
        $response = $this->getJson('/api/modules');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'system',
                'status',
                'timestamp',
                'modules' => [
                    'identity',
                    'attendance',
                    'payroll',
                    'accounting',
                    'inventory',
                    'notification',
                ],
            ]);
    }

    /**
     * Test Identity module index and health endpoints when authorized by Gateway.
     */
    public function test_identity_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/identity');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/identity/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Attendance module index and health endpoints when authorized by Gateway.
     */
    public function test_attendance_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/attendance');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $filterResponse = $this->withHeaders($this->gatewayHeaders())->getJson('/api/attendance?status=present');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/attendance/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Payroll module index and health endpoints when authorized by Gateway.
     */
    public function test_payroll_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/payroll');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/payroll/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Accounting module index and health endpoints when authorized by Gateway.
     */
    public function test_accounting_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/accounting');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $filterResponse = $this->withHeaders($this->gatewayHeaders())->getJson('/api/accounting?type=asset');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/accounting/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Inventory module index and health endpoints when authorized by Gateway.
     */
    public function test_inventory_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/inventory');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $filterResponse = $this->withHeaders($this->gatewayHeaders())->getJson('/api/inventory?status=in_stock');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/inventory/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Notification module index and health endpoints when authorized by Gateway.
     */
    public function test_notification_service_endpoints(): void
    {
        $response = $this->withHeaders($this->gatewayHeaders())->getJson('/api/notification');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'overview' => ['service', 'status', 'features'],
                    'items',
                ],
            ]);

        $filterResponse = $this->withHeaders($this->gatewayHeaders())->getJson('/api/notification?channel=email');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->withHeaders($this->gatewayHeaders())->getJson('/api/notification/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }
}

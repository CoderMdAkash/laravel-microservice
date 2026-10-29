<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModuleMicroserviceEndpointsTest extends TestCase
{
    /**
     * Test global module discovery endpoint.
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
     * Test Identity module index and health endpoints.
     */
    public function test_identity_service_endpoints(): void
    {
        $response = $this->getJson('/api/identity');
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

        $health = $this->getJson('/api/identity/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Attendance module index and health endpoints.
     */
    public function test_attendance_service_endpoints(): void
    {
        $response = $this->getJson('/api/attendance');
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

        $filterResponse = $this->getJson('/api/attendance?status=present');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->getJson('/api/attendance/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Payroll module index and health endpoints.
     */
    public function test_payroll_service_endpoints(): void
    {
        $response = $this->getJson('/api/payroll');
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

        $health = $this->getJson('/api/payroll/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Accounting module index and health endpoints.
     */
    public function test_accounting_service_endpoints(): void
    {
        $response = $this->getJson('/api/accounting');
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

        $filterResponse = $this->getJson('/api/accounting?type=asset');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->getJson('/api/accounting/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Inventory module index and health endpoints.
     */
    public function test_inventory_service_endpoints(): void
    {
        $response = $this->getJson('/api/inventory');
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

        $filterResponse = $this->getJson('/api/inventory?status=in_stock');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->getJson('/api/inventory/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }

    /**
     * Test Notification module index and health endpoints.
     */
    public function test_notification_service_endpoints(): void
    {
        $response = $this->getJson('/api/notification');
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

        $filterResponse = $this->getJson('/api/notification?channel=email');
        $filterResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $health = $this->getJson('/api/notification/health');
        $health->assertStatus(200)
            ->assertJsonPath('data.status', 'operational');
    }
}

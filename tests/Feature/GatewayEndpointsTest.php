<?php

namespace Tests\Feature;

use Tests\TestCase;

class GatewayEndpointsTest extends TestCase
{
    /**
     * Test gateway overview endpoint with telemetry and correlation ID.
     */
    public function test_gateway_overview_returns_telemetry_and_correlation_id(): void
    {
        $response = $this->withHeader('X-Correlation-ID', 'test-uuid-1234')
            ->getJson('/api/gateway/overview');

        $response->assertStatus(200)
            ->assertHeader('X-Correlation-ID', 'test-uuid-1234')
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'gateway' => [
                        'name',
                        'version',
                        'status',
                        'cluster_latency_ms',
                        'total_services',
                        'active_services',
                    ],
                    'services' => [
                        'identity',
                        'attendance',
                        'payroll',
                        'accounting',
                        'inventory',
                        'notification',
                    ],
                ],
            ]);
    }

    /**
     * Test gateway aggregator employee dashboard.
     */
    public function test_gateway_employee_dashboard_aggregates_multiple_services(): void
    {
        $response = $this->getJson('/api/gateway/dashboard/1');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'aggregated_at',
                    'employee' => ['id', 'name', 'email'],
                    'attendance' => ['employee_id', 'status'],
                    'payroll' => ['employee_id', 'net_salary'],
                    'notifications',
                ],
            ]);
    }

    /**
     * Test gateway service dispatching.
     */
    public function test_gateway_dispatch_routes_to_identity(): void
    {
        $response = $this->getJson('/api/gateway/dispatch/identity');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'overview',
                    'items',
                ],
                'meta' => [
                    'correlation_id',
                    'execution_ms',
                ],
            ]);
    }

    /**
     * Test gateway dispatch with invalid service name.
     */
    public function test_gateway_dispatch_returns_404_for_unknown_service(): void
    {
        $response = $this->getJson('/api/gateway/dispatch/unknown-service');

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonStructure([
                'message',
                'errors' => ['available_services'],
            ]);
    }

    /**
     * Test field-level schema documentation endpoint.
     */
    public function test_gateway_docs_returns_field_level_schema(): void
    {
        $response = $this->getJson('/api/gateway/docs');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'openapi',
                    'info',
                    'modules' => [
                        'Identity' => ['fields', 'request_query_parameters'],
                        'Attendance' => ['fields', 'request_query_parameters'],
                        'Payroll' => ['fields', 'request_query_parameters'],
                        'Accounting' => ['fields', 'request_query_parameters'],
                        'Inventory' => ['fields', 'request_query_parameters'],
                        'Notification' => ['fields', 'request_query_parameters'],
                    ],
                ],
            ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Gateway\Controllers;

use App\Gateway\Services\GatewayService;
use App\Http\Controllers\Controller;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(
        protected GatewayService $gatewayService
    ) {}

    /**
     * API Gateway cluster overview and health telemetry.
     */
    public function overview(): JsonResponse
    {
        $telemetry = $this->gatewayService->getClusterTelemetry();

        return ApiResponse::success(
            data: $telemetry,
            message: 'API Gateway cluster telemetry retrieved successfully.'
        );
    }

    /**
     * API Gateway Aggregator: single unified dashboard combining Identity, Attendance, Payroll, and Notifications.
     */
    public function employeeDashboard(int $employeeId): JsonResponse
    {
        $dashboard = $this->gatewayService->getEmployeeDashboard($employeeId);

        return ApiResponse::success(
            data: $dashboard,
            message: "Aggregated employee dashboard for ID {$employeeId} compiled successfully."
        );
    }

    /**
     * Dynamic service dispatcher through API Gateway.
     */
    public function dispatch(string $service, Request $request): JsonResponse
    {
        return $this->gatewayService->dispatch($service, $request);
    }

    /**
     * Field-level OpenApi & microservice schema documentation.
     */
    public function docs(): JsonResponse
    {
        $schema = $this->gatewayService->getApiDocumentationSchema();

        return ApiResponse::success(
            data: $schema,
            message: 'Field-level schema documentation retrieved successfully.'
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Payroll\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payroll\Requests\PayrollIndexRequest;
use App\Modules\Payroll\Services\PayrollService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    /**
     * Test and index Payroll microservice.
     */
    public function index(PayrollIndexRequest $request): JsonResponse
    {
        $status = $this->payrollService->getModuleStatus();
        $payrolls = $this->payrollService->listPayrolls(
            month: $request->input('month'),
            status: $request->input('status'),
            employeeId: $request->filled('employee_id') ? (int) $request->input('employee_id') : null
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $payrolls,
            ],
            message: 'Payroll service operational and responding successfully.'
        );
    }

    /**
     * Payroll service health endpoint.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->payrollService->getModuleStatus(),
            message: 'Payroll health check OK'
        );
    }
}

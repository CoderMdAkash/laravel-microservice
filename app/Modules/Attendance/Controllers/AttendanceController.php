<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Requests\AttendanceIndexRequest;
use App\Modules\Attendance\Services\AttendanceService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Test and index Attendance microservice.
     */
    public function index(AttendanceIndexRequest $request): JsonResponse
    {
        $status = $this->attendanceService->getModuleStatus();
        $records = $this->attendanceService->listRecords(
            date: $request->input('date'),
            status: $request->input('status'),
            employeeId: $request->filled('employee_id') ? (int) $request->input('employee_id') : null
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $records,
            ],
            message: 'Attendance service operational and responding successfully.'
        );
    }

    /**
     * Attendance service health endpoint.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->attendanceService->getModuleStatus(),
            message: 'Attendance health check OK'
        );
    }
}

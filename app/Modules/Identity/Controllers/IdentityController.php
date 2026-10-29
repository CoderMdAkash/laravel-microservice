<?php

declare(strict_types=1);

namespace App\Modules\Identity\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Requests\IdentityIndexRequest;
use App\Modules\Identity\Services\IdentityService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class IdentityController extends Controller
{
    public function __construct(
        protected IdentityService $identityService
    ) {}

    /**
     * Test and index Identity microservice.
     */
    public function index(IdentityIndexRequest $request): JsonResponse
    {
        $status = $this->identityService->getModuleStatus();
        $users = $this->identityService->listUsers(
            search: $request->input('search'),
            role: $request->input('role'),
            status: $request->input('status')
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $users,
            ],
            message: 'Identity service operational and responding successfully.'
        );
    }

    /**
     * Health check endpoint for identity service.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->identityService->getModuleStatus(),
            message: 'Identity health check OK'
        );
    }
}

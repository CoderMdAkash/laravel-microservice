<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Requests\AccountingIndexRequest;
use App\Modules\Accounting\Services\AccountingService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AccountingController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Test and index Accounting microservice.
     */
    public function index(AccountingIndexRequest $request): JsonResponse
    {
        $status = $this->accountingService->getModuleStatus();
        $accounts = $this->accountingService->listAccounts(
            type: $request->input('type'),
            search: $request->input('search')
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $accounts,
            ],
            message: 'Accounting service operational and responding successfully.'
        );
    }

    /**
     * Accounting service health endpoint.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->accountingService->getModuleStatus(),
            message: 'Accounting health check OK'
        );
    }
}

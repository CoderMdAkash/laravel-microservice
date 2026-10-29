<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Requests\InventoryIndexRequest;
use App\Modules\Inventory\Services\InventoryService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Test and index Inventory microservice.
     */
    public function index(InventoryIndexRequest $request): JsonResponse
    {
        $status = $this->inventoryService->getModuleStatus();
        $items = $this->inventoryService->listItems(
            category: $request->input('category'),
            status: $request->input('status'),
            search: $request->input('search')
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $items,
            ],
            message: 'Inventory service operational and responding successfully.'
        );
    }

    /**
     * Inventory service health endpoint.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->inventoryService->getModuleStatus(),
            message: 'Inventory health check OK'
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Requests\NotificationIndexRequest;
use App\Modules\Notification\Services\NotificationService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Test and index Notification microservice.
     */
    public function index(NotificationIndexRequest $request): JsonResponse
    {
        $status = $this->notificationService->getModuleStatus();
        $notifications = $this->notificationService->listNotifications(
            channel: $request->input('channel'),
            status: $request->input('status'),
            recipient: $request->input('recipient')
        );

        return ApiResponse::success(
            data: [
                'overview' => $status,
                'items' => $notifications,
            ],
            message: 'Notification service operational and responding successfully.'
        );
    }

    /**
     * Notification service health endpoint.
     */
    public function health(): JsonResponse
    {
        return ApiResponse::success(
            data: $this->notificationService->getModuleStatus(),
            message: 'Notification health check OK'
        );
    }
}

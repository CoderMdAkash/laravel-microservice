<?php

declare(strict_types=1);

namespace App\Modules\Notification\Services;

use App\Modules\Notification\DTOs\NotificationDTO;

class NotificationService
{
    /**
     * Get notification module status and delivery metrics.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        return [
            'service' => 'Notification Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['multi_channel_routing', 'template_engine', 'retry_mechanism', 'delivery_receipts'],
            'supported_channels' => ['email', 'sms', 'push', 'webhook'],
            'queue_driver' => config('queue.default', 'database'),
        ];
    }

    /**
     * List recent notification dispatches mapped to DTOs.
     *
     * @return list<array<string, mixed>>
     */
    public function listNotifications(?string $channel = null, ?string $status = null, ?string $recipient = null): array
    {
        $notifications = [
            new NotificationDTO(
                id: 1,
                recipient: 'admin@company.com',
                channel: 'email',
                title: 'Monthly Financial Audit Ready',
                content: 'The consolidated accounting reports for Q3 have been generated successfully.',
                status: 'delivered',
                sentAt: now()->subMinutes(12)->toIso8601String()
            ),
            new NotificationDTO(
                id: 2,
                recipient: '+1234567890',
                channel: 'sms',
                title: 'Security Alert: New Sign-in',
                content: 'A new login was recorded from IP 192.168.1.50.',
                status: 'delivered',
                sentAt: now()->subHours(2)->toIso8601String()
            ),
            new NotificationDTO(
                id: 3,
                recipient: 'dev-team-webhook',
                channel: 'webhook',
                title: 'Inventory Alert: Stock Threshold',
                content: 'Dell UltraSharp 27" monitor has reached low stock threshold.',
                status: 'delivered',
                sentAt: now()->subMinutes(35)->toIso8601String()
            ),
        ];

        return collect($notifications)
            ->filter(fn (NotificationDTO $dto) => $channel === null || $dto->channel === $channel)
            ->filter(fn (NotificationDTO $dto) => $status === null || $dto->status === $status)
            ->filter(fn (NotificationDTO $dto) => $recipient === null ||
                str_contains(strtolower($dto->recipient), strtolower($recipient)))
            ->map(fn (NotificationDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }
}

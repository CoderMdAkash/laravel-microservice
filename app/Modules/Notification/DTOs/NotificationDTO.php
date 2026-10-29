<?php

declare(strict_types=1);

namespace App\Modules\Notification\DTOs;

use App\Shared\Support\DataTransferObject;

class NotificationDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $recipient,
        public string $channel,
        public string $title,
        public string $content,
        public string $status = 'delivered',
        public ?string $sentAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            recipient: (string) ($data['recipient'] ?? ''),
            channel: (string) ($data['channel'] ?? 'email'),
            title: (string) ($data['title'] ?? ''),
            content: (string) ($data['content'] ?? ''),
            status: (string) ($data['status'] ?? 'delivered'),
            sentAt: isset($data['sent_at']) ? (string) $data['sent_at'] : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'recipient' => $this->recipient,
            'channel' => $this->channel,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'sent_at' => $this->sentAt,
        ];
    }
}

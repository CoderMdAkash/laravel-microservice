<?php

declare(strict_types=1);

namespace App\Modules\Identity\DTOs;

use App\Shared\Support\DataTransferObject;

class IdentityDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $role = 'User',
        public string $status = 'active',
        public ?string $createdAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            role: (string) ($data['role'] ?? 'User'),
            status: (string) ($data['status'] ?? 'active'),
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Inventory\DTOs;

use App\Shared\Support\DataTransferObject;

class InventoryDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $sku,
        public string $name,
        public string $category,
        public int $quantity,
        public float $unitPrice,
        public string $status = 'in_stock'
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            sku: (string) ($data['sku'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            category: (string) ($data['category'] ?? 'General'),
            quantity: (int) ($data['quantity'] ?? 0),
            unitPrice: (float) ($data['unit_price'] ?? 0.0),
            status: (string) ($data['status'] ?? 'in_stock')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'status' => $this->status,
        ];
    }
}

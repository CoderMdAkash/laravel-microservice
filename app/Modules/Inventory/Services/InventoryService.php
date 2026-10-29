<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\DTOs\InventoryDTO;

class InventoryService
{
    /**
     * Get inventory module status and stock health.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        return [
            'service' => 'Inventory Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['sku_tracking', 'warehouse_allocation', 'reorder_point_alerts', 'barcode_scanning'],
            'warehouse_location' => 'Primary Central Facility (WH-01)',
            'stock_accuracy' => '99.4%',
        ];
    }

    /**
     * List inventory items mapped into DTOs.
     *
     * @return list<array<string, mixed>>
     */
    public function listItems(?string $category = null, ?string $status = null, ?string $search = null): array
    {
        $items = [
            new InventoryDTO(
                id: 1,
                sku: 'SKU-LAP-M3P',
                name: 'Apple MacBook Pro 16" M3 Pro',
                category: 'Hardware',
                quantity: 45,
                unitPrice: 2499.00,
                status: 'in_stock'
            ),
            new InventoryDTO(
                id: 2,
                sku: 'SKU-MON-4K27',
                name: 'Dell UltraSharp 27" 4K USB-C Monitor',
                category: 'Hardware',
                quantity: 8,
                unitPrice: 599.99,
                status: 'low_stock'
            ),
            new InventoryDTO(
                id: 3,
                sku: 'SKU-ACC-KBD',
                name: 'Logitech MX Keys Advanced Wireless Keyboard',
                category: 'Peripherals',
                quantity: 0,
                unitPrice: 119.50,
                status: 'out_of_stock'
            ),
            new InventoryDTO(
                id: 4,
                sku: 'SKU-ACC-MOU',
                name: 'Logitech MX Master 3S Wireless Mouse',
                category: 'Peripherals',
                quantity: 62,
                unitPrice: 99.00,
                status: 'in_stock'
            ),
        ];

        return collect($items)
            ->filter(fn (InventoryDTO $dto) => $category === null || strtolower($dto->category) === strtolower($category))
            ->filter(fn (InventoryDTO $dto) => $status === null || $dto->status === $status)
            ->filter(fn (InventoryDTO $dto) => $search === null ||
                str_contains(strtolower($dto->name), strtolower($search)) ||
                str_contains(strtolower($dto->sku), strtolower($search)))
            ->map(fn (InventoryDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }
}

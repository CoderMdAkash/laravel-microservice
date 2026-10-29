<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Shared\Models\TenantBaseModel;

class InventoryItem extends TenantBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'sku',
        'name',
        'category',
        'quantity',
        'unit_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'float',
        ];
    }
}

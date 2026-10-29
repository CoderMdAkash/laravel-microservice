<?php

declare(strict_types=1);

namespace App\Shared\Models;

use App\Shared\Traits\HasUuid;

class Tenant extends BaseModel
{
    use HasUuid;

    /**
     * Landlord system table.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * Attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'subdomain',
        'database_name',
        'db_host',
        'db_username',
        'status',
        'plan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
            'plan' => 'string',
        ];
    }
}

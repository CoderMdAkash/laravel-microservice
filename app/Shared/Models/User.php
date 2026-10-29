<?php

declare(strict_types=1);

namespace App\Shared\Models;

use App\Shared\Support\TenantContext;
use App\Shared\Traits\BelongsToTenant;
use App\Shared\Traits\HasStatus;
use App\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use BelongsToTenant, HasFactory, HasStatus, HasUuid, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'uuid',
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Dynamic tenant database connection resolution.
     */
    public function getConnectionName(): ?string
    {
        if (TenantContext::hasTenant() && TenantContext::getDatabaseName() !== null) {
            return 'tenant';
        }

        return $this->connection ?? config('database.default');
    }
}


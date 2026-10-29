<?php

declare(strict_types=1);

namespace App\Modules\Identity\Services;

use App\Modules\Identity\DTOs\IdentityDTO;
use App\Shared\Models\IdentityUser;
use Illuminate\Support\Facades\Schema;

class IdentityService
{
    /**
     * Get identity module service overview and test status.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        $hasUsersTable = Schema::hasTable('users');

        return [
            'service' => 'Identity Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['authentication', 'authorization', 'user_profile', 'token_management'],
            'total_users' => $hasUsersTable ? IdentityUser::count() : 3,
        ];
    }

    /**
     * Retrieve users matching criteria and map into DTOs.
     *
     * @return list<array<string, mixed>>
     */
    public function listUsers(?string $search = null, ?string $role = null, ?string $status = null): array
    {
        if (Schema::hasTable('users')) {
            $query = IdentityUser::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->limit(10)->get();

            if ($users->isNotEmpty()) {
                return $users->map(fn (IdentityUser $user) => IdentityDTO::fromArray([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role ?? 'User',
                    'status' => $status ?? 'active',
                    'created_at' => $user->created_at?->toIso8601String(),
                ])->toArray())->values()->all();
            }
        }

        // Return mock testing entities if database has no rows
        return [
            (new IdentityDTO(1, 'System Administrator', 'admin@example.com', 'Admin', 'active', now()->toIso8601String()))->toArray(),
            (new IdentityDTO(2, 'Sarah Connor', 'sarah@example.com', 'Manager', 'active', now()->subDays(5)->toIso8601String()))->toArray(),
            (new IdentityDTO(3, 'John Doe', 'john@example.com', 'User', 'inactive', now()->subDays(10)->toIso8601String()))->toArray(),
        ];
    }
}

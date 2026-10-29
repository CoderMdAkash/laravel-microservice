<?php

declare(strict_types=1);

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    /**
     * Scope query to active records.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query by a specific status string.
     *
     * @param  Builder<static>  $query
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the current model is marked as active.
     */
    public function isActive(): bool
    {
        return ($this->status ?? '') === 'active';
    }
}

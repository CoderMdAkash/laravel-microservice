<?php

declare(strict_types=1);

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and auto-assign UUID on model creation if needed.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Scope query to find by UUID.
     *
     * @param  Builder<static>  $query
     */
    public function scopeWhereUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}

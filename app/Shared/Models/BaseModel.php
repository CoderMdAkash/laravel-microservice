<?php

declare(strict_types=1);

namespace App\Shared\Models;

use App\Shared\Traits\HasStatus;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use HasFactory, HasStatus;

    /**
     * Default primary key type.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Prepare a date for array / JSON serialization as ISO-8601.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(DateTimeInterface::ATOM);
    }

    /**
     * Scope query to order by most recent creation date.
     *
     * @param  Builder<static>  $query
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc($this->getTable().'.created_at');
    }

    /**
     * Scope query for simple substring search.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSearch(Builder $query, string $column, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where($column, 'like', "%{$term}%");
    }
}

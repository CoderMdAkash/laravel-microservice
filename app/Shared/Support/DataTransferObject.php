<?php

declare(strict_types=1);

namespace App\Shared\Support;

use JsonSerializable;

abstract class DataTransferObject implements JsonSerializable
{
    /**
     * Convert DTO properties to an array.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * Serialization handler for json_encode().
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

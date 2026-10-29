<?php

declare(strict_types=1);

namespace App\Modules\Accounting\DTOs;

use App\Shared\Support\DataTransferObject;

class AccountingDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $accountCode,
        public string $accountName,
        public string $type,
        public float $balance,
        public string $currency = 'USD'
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            accountCode: (string) ($data['account_code'] ?? ''),
            accountName: (string) ($data['account_name'] ?? ''),
            type: (string) ($data['type'] ?? 'asset'),
            balance: (float) ($data['balance'] ?? 0.0),
            currency: (string) ($data['currency'] ?? 'USD')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'account_code' => $this->accountCode,
            'account_name' => $this->accountName,
            'type' => $this->type,
            'balance' => $this->balance,
            'currency' => $this->currency,
        ];
    }
}

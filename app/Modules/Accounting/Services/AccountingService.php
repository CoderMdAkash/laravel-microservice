<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\DTOs\AccountingDTO;

class AccountingService
{
    /**
     * Get accounting module status and financial summary.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        return [
            'service' => 'Accounting Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['double_entry_ledger', 'invoice_generation', 'tax_compliance', 'financial_reporting'],
            'base_currency' => 'USD',
            'fiscal_year' => date('Y'),
        ];
    }

    /**
     * List accounts mapped into DTOs.
     *
     * @return list<array<string, mixed>>
     */
    public function listAccounts(?string $type = null, ?string $search = null): array
    {
        $accounts = [
            new AccountingDTO(
                id: 1,
                accountCode: 'AC-1001',
                accountName: 'Main Operating Cash Account',
                type: 'asset',
                balance: 148500.50,
                currency: 'USD'
            ),
            new AccountingDTO(
                id: 2,
                accountCode: 'AC-2001',
                accountName: 'Accounts Payable - Vendors',
                type: 'liability',
                balance: 32000.00,
                currency: 'USD'
            ),
            new AccountingDTO(
                id: 3,
                accountCode: 'AC-4001',
                accountName: 'Enterprise SaaS Subscription Revenue',
                type: 'revenue',
                balance: 295000.00,
                currency: 'USD'
            ),
            new AccountingDTO(
                id: 4,
                accountCode: 'AC-5001',
                accountName: 'Cloud Infrastructure & Hosting Expenses',
                type: 'expense',
                balance: 18450.75,
                currency: 'USD'
            ),
        ];

        return collect($accounts)
            ->filter(fn (AccountingDTO $dto) => $type === null || $dto->type === $type)
            ->filter(fn (AccountingDTO $dto) => $search === null ||
                str_contains(strtolower($dto->accountName), strtolower($search)) ||
                str_contains(strtolower($dto->accountCode), strtolower($search)))
            ->map(fn (AccountingDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Payroll\Services;

use App\Modules\Payroll\DTOs\PayrollDTO;

class PayrollService
{
    /**
     * Get payroll module status and ledger overview.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        return [
            'service' => 'Payroll Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['salary_computation', 'tax_deduction', 'bonus_calculation', 'payslip_generation'],
            'current_cycle' => date('Y-m'),
            'disbursement_status' => 'scheduled',
        ];
    }

    /**
     * List payroll records mapped into DTOs.
     *
     * @return list<array<string, mixed>>
     */
    public function listPayrolls(?string $month = null, ?string $status = null, ?int $employeeId = null): array
    {
        $targetMonth = $month ?? date('Y-m');

        $payrolls = [
            new PayrollDTO(
                id: 501,
                employeeId: 1,
                employeeName: 'Alice Morgan',
                month: $targetMonth,
                basicSalary: 6500.00,
                allowances: 1200.00,
                deductions: 750.00,
                netSalary: 6950.00,
                status: 'paid'
            ),
            new PayrollDTO(
                id: 502,
                employeeId: 2,
                employeeName: 'Bob Jenkins',
                month: $targetMonth,
                basicSalary: 5200.00,
                allowances: 800.00,
                deductions: 500.00,
                netSalary: 5500.00,
                status: 'processed'
            ),
            new PayrollDTO(
                id: 503,
                employeeId: 3,
                employeeName: 'Charlie Adams',
                month: $targetMonth,
                basicSalary: 4800.00,
                allowances: 600.00,
                deductions: 450.00,
                netSalary: 4950.00,
                status: 'draft'
            ),
        ];

        return collect($payrolls)
            ->filter(fn (PayrollDTO $dto) => $status === null || $dto->status === $status)
            ->filter(fn (PayrollDTO $dto) => $employeeId === null || $dto->employeeId === $employeeId)
            ->map(fn (PayrollDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }
}

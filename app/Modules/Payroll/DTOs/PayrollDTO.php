<?php

declare(strict_types=1);

namespace App\Modules\Payroll\DTOs;

use App\Shared\Support\DataTransferObject;

class PayrollDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $employeeId,
        public string $employeeName,
        public string $month,
        public float $basicSalary,
        public float $allowances,
        public float $deductions,
        public float $netSalary,
        public string $status = 'paid'
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $basic = (float) ($data['basic_salary'] ?? 0.0);
        $allowances = (float) ($data['allowances'] ?? 0.0);
        $deductions = (float) ($data['deductions'] ?? 0.0);
        $net = (float) ($data['net_salary'] ?? ($basic + $allowances - $deductions));

        return new self(
            id: (int) ($data['id'] ?? 0),
            employeeId: (int) ($data['employee_id'] ?? 0),
            employeeName: (string) ($data['employee_name'] ?? ''),
            month: (string) ($data['month'] ?? date('Y-m')),
            basicSalary: $basic,
            allowances: $allowances,
            deductions: $deductions,
            netSalary: $net,
            status: (string) ($data['status'] ?? 'paid')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employeeId,
            'employee_name' => $this->employeeName,
            'month' => $this->month,
            'basic_salary' => $this->basicSalary,
            'allowances' => $this->allowances,
            'deductions' => $this->deductions,
            'net_salary' => $this->netSalary,
            'status' => $this->status,
        ];
    }
}

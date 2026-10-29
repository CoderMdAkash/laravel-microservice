<?php

declare(strict_types=1);

namespace App\Modules\Payroll\Models;

use App\Shared\Models\TenantBaseModel;

class PayrollRecord extends TenantBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payroll_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'employee_id',
        'employee_name',
        'month',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'basic_salary' => 'float',
            'allowances' => 'float',
            'deductions' => 'float',
            'net_salary' => 'float',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Models;

class AttendanceRecord extends TenantBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'employee_id',
        'employee_name',
        'check_in',
        'check_out',
        'status',
        'work_hours',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in' => 'datetime',
            'check_out' => 'datetime',
            'work_hours' => 'float',
        ];
    }
}


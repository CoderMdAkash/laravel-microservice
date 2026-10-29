<?php

declare(strict_types=1);

namespace App\Modules\Attendance\DTOs;

use App\Shared\Support\DataTransferObject;

class AttendanceDTO extends DataTransferObject
{
    public function __construct(
        public int $id,
        public int $employeeId,
        public string $employeeName,
        public string $checkIn,
        public ?string $checkOut = null,
        public string $status = 'present',
        public float $workHours = 8.0
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            employeeId: (int) ($data['employee_id'] ?? 0),
            employeeName: (string) ($data['employee_name'] ?? ''),
            checkIn: (string) ($data['check_in'] ?? ''),
            checkOut: isset($data['check_out']) ? (string) $data['check_out'] : null,
            status: (string) ($data['status'] ?? 'present'),
            workHours: (float) ($data['work_hours'] ?? 8.0)
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
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'status' => $this->status,
            'work_hours' => $this->workHours,
        ];
    }
}

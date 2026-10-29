<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\DTOs\AttendanceDTO;
use Illuminate\Support\Carbon;

class AttendanceService
{
    /**
     * Get attendance microservice status and operational telemetry.
     *
     * @return array<string, mixed>
     */
    public function getModuleStatus(): array
    {
        return [
            'service' => 'Attendance Microservice',
            'status' => 'operational',
            'version' => '1.0.0',
            'features' => ['biometric_sync', 'geofence_checkin', 'shift_scheduling', 'overtime_calculation'],
            'today' => Carbon::today()->toDateString(),
            'active_shifts' => 3,
        ];
    }

    /**
     * List attendance records mapped to DTO instances.
     *
     * @return list<array<string, mixed>>
     */
    public function listRecords(?string $date = null, ?string $status = null, ?int $employeeId = null): array
    {
        $today = $date ?? Carbon::today()->toDateString();

        $records = [
            new AttendanceDTO(
                id: 101,
                employeeId: 1,
                employeeName: 'Alice Morgan',
                checkIn: "{$today} 08:55:00",
                checkOut: "{$today} 17:05:00",
                status: 'present',
                workHours: 8.16
            ),
            new AttendanceDTO(
                id: 102,
                employeeId: 2,
                employeeName: 'Bob Jenkins',
                checkIn: "{$today} 09:35:00",
                checkOut: null,
                status: 'late',
                workHours: 6.5
            ),
            new AttendanceDTO(
                id: 103,
                employeeId: 3,
                employeeName: 'Charlie Adams',
                checkIn: "{$today} 08:45:00",
                checkOut: "{$today} 17:00:00",
                status: 'present',
                workHours: 8.25
            ),
        ];

        return collect($records)
            ->filter(fn (AttendanceDTO $dto) => $status === null || $dto->status === $status)
            ->filter(fn (AttendanceDTO $dto) => $employeeId === null || $dto->employeeId === $employeeId)
            ->map(fn (AttendanceDTO $dto) => $dto->toArray())
            ->values()
            ->all();
    }
}

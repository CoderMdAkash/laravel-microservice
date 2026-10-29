<?php

declare(strict_types=1);

namespace App\Gateway\Services;

use App\Modules\Accounting\Services\AccountingService;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Identity\Services\IdentityService;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Payroll\Services\PayrollService;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GatewayService
{
    /**
     * Microservice service registry.
     */
    public function __construct(
        protected IdentityService $identityService,
        protected AttendanceService $attendanceService,
        protected PayrollService $payrollService,
        protected AccountingService $accountingService,
        protected InventoryService $inventoryService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Get aggregate health status and metrics across all microservices.
     *
     * @return array<string, mixed>
     */
    public function getClusterTelemetry(): array
    {
        $startTime = microtime(true);

        $services = [
            'identity' => $this->identityService->getModuleStatus(),
            'attendance' => $this->attendanceService->getModuleStatus(),
            'payroll' => $this->payrollService->getModuleStatus(),
            'accounting' => $this->accountingService->getModuleStatus(),
            'inventory' => $this->inventoryService->getModuleStatus(),
            'notification' => $this->notificationService->getModuleStatus(),
        ];

        $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'gateway' => [
                'name' => 'Laravel Microservice API Gateway',
                'version' => '1.0.0',
                'status' => 'healthy',
                'cluster_latency_ms' => $latencyMs,
                'total_services' => count($services),
                'active_services' => count($services),
            ],
            'services' => $services,
        ];
    }

    /**
     * Gateway Aggregator Pattern: aggregates Identity, Attendance, Payroll, and Notification data for an employee.
     *
     * @return array<string, mixed>
     */
    public function getEmployeeDashboard(int $employeeId): array
    {
        // 1. Fetch user from Identity service
        $users = $this->identityService->listUsers();
        $user = collect($users)->firstWhere('id', $employeeId) ?? [
            'id' => $employeeId,
            'name' => "Employee #{$employeeId}",
            'email' => "employee{$employeeId}@company.com",
            'role' => 'Staff',
            'status' => 'active',
        ];

        // 2. Fetch today's Attendance
        $attendances = $this->attendanceService->listRecords(employeeId: $employeeId);
        $attendance = $attendances[0] ?? [
            'employee_id' => $employeeId,
            'status' => 'unrecorded',
            'check_in' => null,
            'check_out' => null,
            'work_hours' => 0.0,
        ];

        // 3. Fetch latest Payroll record
        $payrolls = $this->payrollService->listPayrolls(employeeId: $employeeId);
        $payroll = $payrolls[0] ?? [
            'employee_id' => $employeeId,
            'month' => date('Y-m'),
            'basic_salary' => 0.0,
            'allowances' => 0.0,
            'deductions' => 0.0,
            'net_salary' => 0.0,
            'status' => 'pending',
        ];

        // 4. Fetch recent relevant Notifications
        $notifications = $this->notificationService->listNotifications();

        return [
            'aggregated_at' => now()->toIso8601String(),
            'employee' => $user,
            'attendance' => $attendance,
            'payroll' => $payroll,
            'notifications' => array_slice($notifications, 0, 3),
        ];
    }

    /**
     * Dynamic Service Dispatcher: routes request to specific microservice.
     */
    public function dispatch(string $service, Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $correlationId = (string) $request->header('X-Correlation-ID', '');

        return match (strtolower($service)) {
            'identity' => ApiResponse::success(
                data: [
                    'overview' => $this->identityService->getModuleStatus(),
                    'items' => $this->identityService->listUsers(
                        search: $request->query('search'),
                        role: $request->query('role'),
                        status: $request->query('status')
                    ),
                ],
                message: 'Gateway dispatched to Identity Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            'attendance' => ApiResponse::success(
                data: [
                    'overview' => $this->attendanceService->getModuleStatus(),
                    'items' => $this->attendanceService->listRecords(
                        date: $request->query('date'),
                        status: $request->query('status'),
                        employeeId: $request->filled('employee_id') ? (int) $request->query('employee_id') : null
                    ),
                ],
                message: 'Gateway dispatched to Attendance Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            'payroll' => ApiResponse::success(
                data: [
                    'overview' => $this->payrollService->getModuleStatus(),
                    'items' => $this->payrollService->listPayrolls(
                        month: $request->query('month'),
                        status: $request->query('status'),
                        employeeId: $request->filled('employee_id') ? (int) $request->query('employee_id') : null
                    ),
                ],
                message: 'Gateway dispatched to Payroll Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            'accounting' => ApiResponse::success(
                data: [
                    'overview' => $this->accountingService->getModuleStatus(),
                    'items' => $this->accountingService->listAccounts(
                        type: $request->query('type'),
                        search: $request->query('search')
                    ),
                ],
                message: 'Gateway dispatched to Accounting Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            'inventory' => ApiResponse::success(
                data: [
                    'overview' => $this->inventoryService->getModuleStatus(),
                    'items' => $this->inventoryService->listItems(
                        category: $request->query('category'),
                        status: $request->query('status'),
                        search: $request->query('search')
                    ),
                ],
                message: 'Gateway dispatched to Inventory Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            'notification' => ApiResponse::success(
                data: [
                    'overview' => $this->notificationService->getModuleStatus(),
                    'items' => $this->notificationService->listNotifications(
                        channel: $request->query('channel'),
                        status: $request->query('status'),
                        recipient: $request->query('recipient')
                    ),
                ],
                message: 'Gateway dispatched to Notification Service',
                meta: [
                    'correlation_id' => $correlationId,
                    'execution_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]
            ),
            default => ApiResponse::error(
                message: "Service '{$service}' is not registered with the API Gateway.",
                statusCode: 404,
                errors: [
                    'available_services' => ['identity', 'attendance', 'payroll', 'accounting', 'inventory', 'notification'],
                ]
            ),
        };
    }

    /**
     * Get field-level schema documentation for all microservice modules.
     *
     * @return array<string, mixed>
     */
    public function getApiDocumentationSchema(): array
    {
        return [
            'openapi' => '3.1.0',
            'info' => [
                'title' => 'Laravel Microservice & API Gateway Specification',
                'version' => '1.0.0',
                'description' => 'Comprehensive field-level schema definitions for all microservices in the modular architecture.',
            ],
            'modules' => [
                'Identity' => [
                    'description' => 'User identity, authentication, profile and role administration.',
                    'model' => 'App\Modules\Identity\Models\IdentityUser',
                    'dto' => 'App\Modules\Identity\DTOs\IdentityDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique internal identity user ID', 'example' => 1],
                        'name' => ['type' => 'string', 'required' => true, 'description' => 'Full name of the user', 'example' => 'System Administrator'],
                        'email' => ['type' => 'string', 'required' => true, 'format' => 'email', 'description' => 'Unique email address', 'example' => 'admin@example.com'],
                        'role' => ['type' => 'string', 'required' => false, 'enum' => ['Admin', 'Manager', 'User'], 'default' => 'User', 'description' => 'User role level', 'example' => 'Admin'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['active', 'inactive'], 'default' => 'active', 'description' => 'Account activation status', 'example' => 'active'],
                        'created_at' => ['type' => 'string', 'required' => false, 'format' => 'date-time', 'description' => 'ISO-8601 creation timestamp', 'example' => '2026-10-29T10:00:00Z'],
                    ],
                    'request_query_parameters' => [
                        'search' => ['type' => 'string', 'required' => false, 'max' => 100, 'description' => 'Substring search in name or email'],
                        'role' => ['type' => 'string', 'required' => false, 'enum' => ['Admin', 'Manager', 'User'], 'description' => 'Filter by role'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['active', 'inactive'], 'description' => 'Filter by status'],
                    ],
                ],
                'Attendance' => [
                    'description' => 'Employee daily check-in, shift tracking, and working hours calculation.',
                    'model' => 'App\Modules\Attendance\Models\AttendanceRecord',
                    'dto' => 'App\Modules\Attendance\DTOs\AttendanceDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique attendance record ID', 'example' => 101],
                        'employee_id' => ['type' => 'integer', 'required' => true, 'description' => 'Associated employee user identifier', 'example' => 1],
                        'employee_name' => ['type' => 'string', 'required' => true, 'description' => 'Name of the employee', 'example' => 'Alice Morgan'],
                        'check_in' => ['type' => 'string', 'required' => true, 'format' => 'date-time', 'description' => 'Recorded check-in timestamp', 'example' => '2026-10-29 08:55:00'],
                        'check_out' => ['type' => 'string', 'required' => false, 'nullable' => true, 'format' => 'date-time', 'description' => 'Recorded check-out timestamp', 'example' => '2026-10-29 17:05:00'],
                        'status' => ['type' => 'string', 'required' => true, 'enum' => ['present', 'late', 'absent', 'on_leave'], 'description' => 'Attendance status badge', 'example' => 'present'],
                        'work_hours' => ['type' => 'float', 'required' => true, 'description' => 'Calculated working hours for the day', 'example' => 8.16],
                    ],
                    'request_query_parameters' => [
                        'date' => ['type' => 'string', 'required' => false, 'format' => 'date', 'description' => 'Target calendar date (YYYY-MM-DD)'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['present', 'late', 'absent', 'on_leave'], 'description' => 'Filter by attendance state'],
                        'employee_id' => ['type' => 'integer', 'required' => false, 'description' => 'Filter by employee identifier'],
                    ],
                ],
                'Payroll' => [
                    'description' => 'Salary computations, gross-to-net breakdown, tax deductions, and payment statuses.',
                    'model' => 'App\Modules\Payroll\Models\PayrollRecord',
                    'dto' => 'App\Modules\Payroll\DTOs\PayrollDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique payroll slip ID', 'example' => 501],
                        'employee_id' => ['type' => 'integer', 'required' => true, 'description' => 'Target employee user ID', 'example' => 1],
                        'employee_name' => ['type' => 'string', 'required' => true, 'description' => 'Target employee full name', 'example' => 'Alice Morgan'],
                        'month' => ['type' => 'string', 'required' => true, 'pattern' => '^\d{4}-(0[1-9]|1[0-2])$', 'description' => 'Salary billing cycle year-month', 'example' => '2026-10'],
                        'basic_salary' => ['type' => 'float', 'required' => true, 'description' => 'Base fixed monthly compensation', 'example' => 6500.00],
                        'allowances' => ['type' => 'float', 'required' => true, 'description' => 'Total bonuses and allowances added', 'example' => 1200.00],
                        'deductions' => ['type' => 'float', 'required' => true, 'description' => 'Total tax and insurance deductions', 'example' => 750.00],
                        'net_salary' => ['type' => 'float', 'required' => true, 'description' => 'Net payable compensation (basic + allowances - deductions)', 'example' => 6950.00],
                        'status' => ['type' => 'string', 'required' => true, 'enum' => ['draft', 'processed', 'paid'], 'description' => 'Disbursement status', 'example' => 'paid'],
                    ],
                    'request_query_parameters' => [
                        'month' => ['type' => 'string', 'required' => false, 'pattern' => '^\d{4}-(0[1-9]|1[0-2])$', 'description' => 'Filter by billing month (YYYY-MM)'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['draft', 'processed', 'paid'], 'description' => 'Filter by disbursement state'],
                        'employee_id' => ['type' => 'integer', 'required' => false, 'description' => 'Filter by employee identifier'],
                    ],
                ],
                'Accounting' => [
                    'description' => 'General ledger, charts of accounts, cash flow, asset and liability tracking.',
                    'model' => 'App\Modules\Accounting\Models\AccountLedger',
                    'dto' => 'App\Modules\Accounting\DTOs\AccountingDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique ledger account ID', 'example' => 1],
                        'account_code' => ['type' => 'string', 'required' => true, 'description' => 'Standard chart of accounts alphanumeric code', 'example' => 'AC-1001'],
                        'account_name' => ['type' => 'string', 'required' => true, 'description' => 'Human-readable title of the account', 'example' => 'Main Operating Cash Account'],
                        'type' => ['type' => 'string', 'required' => true, 'enum' => ['asset', 'liability', 'equity', 'revenue', 'expense'], 'description' => 'Accounting category classification', 'example' => 'asset'],
                        'balance' => ['type' => 'float', 'required' => true, 'description' => 'Current running monetary balance', 'example' => 148500.50],
                        'currency' => ['type' => 'string', 'required' => true, 'default' => 'USD', 'description' => '3-letter ISO-4217 currency code', 'example' => 'USD'],
                    ],
                    'request_query_parameters' => [
                        'type' => ['type' => 'string', 'required' => false, 'enum' => ['asset', 'liability', 'equity', 'revenue', 'expense'], 'description' => 'Filter by accounting type'],
                        'search' => ['type' => 'string', 'required' => false, 'max' => 100, 'description' => 'Substring search in code or title'],
                    ],
                ],
                'Inventory' => [
                    'description' => 'Product stock control, SKU tracking, warehouse allocation, and availability monitoring.',
                    'model' => 'App\Modules\Inventory\Models\InventoryItem',
                    'dto' => 'App\Modules\Inventory\DTOs\InventoryDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique inventory item ID', 'example' => 1],
                        'sku' => ['type' => 'string', 'required' => true, 'description' => 'Stock Keeping Unit unique barcode/code', 'example' => 'SKU-LAP-M3P'],
                        'name' => ['type' => 'string', 'required' => true, 'description' => 'Product title / item description', 'example' => 'Apple MacBook Pro 16" M3 Pro'],
                        'category' => ['type' => 'string', 'required' => true, 'description' => 'Product family / categorization', 'example' => 'Hardware'],
                        'quantity' => ['type' => 'integer', 'required' => true, 'description' => 'Physical stock count available in warehouse', 'example' => 45],
                        'unit_price' => ['type' => 'float', 'required' => true, 'description' => 'Selling price per single unit', 'example' => 2499.00],
                        'status' => ['type' => 'string', 'required' => true, 'enum' => ['in_stock', 'low_stock', 'out_of_stock'], 'description' => 'Stock status warning indicator', 'example' => 'in_stock'],
                    ],
                    'request_query_parameters' => [
                        'category' => ['type' => 'string', 'required' => false, 'max' => 50, 'description' => 'Filter by category'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['in_stock', 'low_stock', 'out_of_stock'], 'description' => 'Filter by stock condition'],
                        'search' => ['type' => 'string', 'required' => false, 'max' => 100, 'description' => 'Substring search in name or SKU'],
                    ],
                ],
                'Notification' => [
                    'description' => 'Multi-channel messaging dispatches (Email, SMS, Push, Webhooks) and delivery logs.',
                    'model' => 'App\Modules\Notification\Models\NotificationLog',
                    'dto' => 'App\Modules\Notification\DTOs\NotificationDTO',
                    'fields' => [
                        'id' => ['type' => 'integer', 'required' => true, 'description' => 'Unique notification log ID', 'example' => 1],
                        'recipient' => ['type' => 'string', 'required' => true, 'description' => 'Destination address (email, phone number, webhook ID)', 'example' => 'admin@company.com'],
                        'channel' => ['type' => 'string', 'required' => true, 'enum' => ['email', 'sms', 'push', 'webhook'], 'description' => 'Delivery communication channel', 'example' => 'email'],
                        'title' => ['type' => 'string', 'required' => true, 'description' => 'Notification subject line / headline', 'example' => 'Monthly Financial Audit Ready'],
                        'content' => ['type' => 'string', 'required' => true, 'description' => 'Complete notification text body payload', 'example' => 'The consolidated accounting reports for Q3 have been generated.'],
                        'status' => ['type' => 'string', 'required' => true, 'enum' => ['pending', 'delivered', 'failed'], 'description' => 'Transmission status receipt', 'example' => 'delivered'],
                        'sent_at' => ['type' => 'string', 'required' => false, 'nullable' => true, 'format' => 'date-time', 'description' => 'ISO-8601 dispatch completion timestamp', 'example' => '2026-10-29T10:30:00Z'],
                    ],
                    'request_query_parameters' => [
                        'channel' => ['type' => 'string', 'required' => false, 'enum' => ['email', 'sms', 'push', 'webhook'], 'description' => 'Filter by channel'],
                        'status' => ['type' => 'string', 'required' => false, 'enum' => ['pending', 'delivered', 'failed'], 'description' => 'Filter by delivery status'],
                        'recipient' => ['type' => 'string', 'required' => false, 'max' => 100, 'description' => 'Filter by recipient target substring'],
                    ],
                ],
            ],
        ];
    }
}

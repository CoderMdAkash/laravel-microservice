<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use App\Shared\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantNotFoundException extends Exception
{
    public function __construct(
        string $message = 'SaaS Tenant not found or inactive',
        int $code = 404
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(
            message: $this->getMessage(),
            statusCode: 404,
            errors: [
                'hint' => 'Ensure a valid X-Tenant-ID header, tenant_id parameter, or registered tenant subdomain is provided.',
            ]
        );
    }
}

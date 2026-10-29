<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use App\Shared\Support\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleException extends Exception
{
    /**
     * @param  array<string, mixed>|null  $errors
     */
    public function __construct(
        string $message = 'Module execution error',
        int $code = 400,
        protected ?array $errors = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(
            message: $this->getMessage(),
            statusCode: $this->getCode() >= 400 && $this->getCode() < 600 ? $this->getCode() : 400,
            errors: $this->errors
        );
    }
}

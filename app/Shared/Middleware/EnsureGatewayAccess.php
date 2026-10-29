<?php

declare(strict_types=1);

namespace App\Shared\Middleware;

use App\Shared\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGatewayAccess
{
    /**
     * Enforce that microservice endpoints can only be accessed through the API Gateway (Port 8000).
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedSecret = (string) config('microservices.gateway_secret');
        $providedSecret = (string) $request->header('X-Gateway-Secret', '');

        // If secret does not match, reject direct external access
        if ($expectedSecret !== '' && $providedSecret !== $expectedSecret) {
            $serviceName = (string) $request->segment(2);
            $gatewayUrl = config('services.gateway.url', 'http://127.0.0.1:8000')."/api/gateway/dispatch/{$serviceName}";

            return ApiResponse::error(
                message: 'Direct microservice access denied. All requests must route through the API Gateway.',
                statusCode: 403,
                errors: [
                    'reason' => 'Direct port access is blocked for security.',
                    'gateway_entrypoint' => $gatewayUrl,
                    'hint' => 'Send your request to the API Gateway at port 8000, or provide the internal X-Gateway-Secret header.',
                ]
            );
        }

        return $next($request);
    }
}

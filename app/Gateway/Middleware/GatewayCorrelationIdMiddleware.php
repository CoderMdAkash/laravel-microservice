<?php

declare(strict_types=1);

namespace App\Gateway\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GatewayCorrelationIdMiddleware
{
    /**
     * Handle an incoming request and ensure a correlation ID exists.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header('X-Correlation-ID');

        if (empty($correlationId)) {
            $correlationId = (string) Str::uuid();
            $request->headers->set('X-Correlation-ID', $correlationId);
        }

        $response = $next($request);

        $response->headers->set('X-Correlation-ID', $correlationId);

        return $response;
    }
}

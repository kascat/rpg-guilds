<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    // Use for debugging only
    // Enable in Kernel.php
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only errors >= 400 (ex.: 4xx ou 5xx)
        if ($response->status() >= 400) {
            Log::channel('api')->error('API Error:', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'input' => $request->all(),
                'status' => $response->status(),
                'content' => $response->getContent(),
            ]);
        }

        return $response;
    }
}

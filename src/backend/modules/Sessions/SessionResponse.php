<?php

namespace Sessions;

use Illuminate\Http\JsonResponse;
use Kascat\EasyModule\Core\Response;

/**
 * Response interceptor
 * Trait SessionResponse
 */
trait SessionResponse
{
    use Response;

    public function responseToIndex(mixed $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }
}

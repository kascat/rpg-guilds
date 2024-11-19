<?php

namespace Guilds;

use Illuminate\Http\JsonResponse;
use Kascat\EasyModule\Core\Response;

/**
 * Response interceptor
 * Trait GuildResponse
 */
trait GuildResponse
{
    use Response;

    public function responseToIndex(mixed $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode);
    }
}

<?php

namespace Tools;

use App\Http\Controllers\Controller;

/**
 * Class ToolController
 */
class ToolController extends Controller
{
    public function __construct(private readonly ToolService $toolService)
    {
        //
    }

    public function qrcode(ToolRequest $request): mixed
    {
        return $this->toolService->qrcode($request->validated('text'));
    }
}

<?php

namespace Tools;

use Kascat\EasyModule\Core\Request;

/**
 * Class ToolRequest
 */
class ToolRequest extends Request
{
    public function validateToQrcode(): array
    {
        return [
            'text' => ['required', 'string'],
        ];
    }
}

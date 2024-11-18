<?php

namespace Tools;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Kascat\EasyModule\Core\Service;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ToolService
 */
class ToolService extends Service
{
    public function qrcode(string $text): BinaryFileResponse
    {
        $renderer = new ImageRenderer(
            new RendererStyle(250),
            new ImagickImageBackEnd()
        );

        $writer = new Writer($renderer);

        $timestamp = Carbon::now()->timestamp;

        $tempFile = "/tmp/temp_$timestamp";

        $writer->writeFile($text, $tempFile);

        return response()->file($tempFile);
    }
}

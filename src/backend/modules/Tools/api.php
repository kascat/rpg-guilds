<?php

/**
 * Easy Module library
 * Packagist: https://packagist.org/packages/kascat/easy-module
 * GitHub: https://github.com/kascat/easy-module
 */

use Illuminate\Support\Facades\Route;
use Tools\ToolController;

Route::group([
    'middleware' => ['api']
], function () {
    Route::get('qrcode', [ToolController::class, 'qrcode'])->name('qrcode');
});

<?php

/**
 * Easy Module library
 * Packagist: https://packagist.org/packages/kascat/easy-module
 * GitHub: https://github.com/kascat/easy-module
 */

use Illuminate\Support\Facades\Route;
use Guilds\GuildController;

Route::group([
    'middleware' => ['api', 'auth:user', 'user_checker']
], function () {
    // Route::apiResource('guilds', GuildController::class);
});

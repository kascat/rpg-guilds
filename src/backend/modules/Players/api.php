<?php

/**
 * Easy Module library
 * Packagist: https://packagist.org/packages/kascat/easy-module
 * GitHub: https://github.com/kascat/easy-module
 */

use Illuminate\Support\Facades\Route;
use Permissions\Enums\AbilitiesEnum;
use Players\PlayerController;

Route::group([
    'middleware' => ['api', 'auth:user', 'user_checker']
], function () {
    Route::apiResource('players', PlayerController::class)
        ->middleware(AbilitiesEnum::requireAllAbilities([AbilitiesEnum::MANAGE_PLAYERS]));
});

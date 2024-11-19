<?php

/**
 * Easy Module library
 * Packagist: https://packagist.org/packages/kascat/easy-module
 * GitHub: https://github.com/kascat/easy-module
 */

use Illuminate\Support\Facades\Route;
use Permissions\Enums\AbilitiesEnum;
use Sessions\SessionController;

Route::group([
    'middleware' => ['api', 'auth:user', 'user_checker']
], function () {
    Route::apiResource('sessions', SessionController::class)
        ->except('update')
        ->middleware(AbilitiesEnum::requireAllAbilities([AbilitiesEnum::MANAGE_SESSIONS]));
});

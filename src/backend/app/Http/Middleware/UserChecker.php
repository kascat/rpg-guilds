<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use StorePlans\StorePlan;
use Users\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserChecker
{
    public function handle($request, Closure $next)
    {
        /** @var User|null $loggedUser */
        $loggedUser = Auth::user();

        if ($loggedUser?->status !== User::STATUS_ACTIVE) {
            throw new HttpResponseException(response()->json(['message' => 'Usuário inativo'], Response::HTTP_UNAUTHORIZED));
        }

        $store = $loggedUser->store;

        if (null === $store) {
            return $next($request);
        }

        $storePlanExpiresIn = $store->storePlan->expires_in->setTime(23, 59, 59);
        $expiresInWithToleranceDays = $storePlanExpiresIn->clone()->addDays(StorePlan::PLAN_TOLERANCE_DAYS);

        if (Carbon::now() > $expiresInWithToleranceDays) {
            throw new HttpResponseException(response()->json(['message' => 'Plano expirado'], Response::HTTP_UNAUTHORIZED));
        }

        return $next($request);
    }
}

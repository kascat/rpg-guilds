<?php

namespace Sessions;

use Illuminate\Database\Eloquent\Builder;
use Users\User;

/**
 * Class SessionRepository
 * Build commom module queries here
 */
class SessionRepository
{
    public static function findOrFail(int $id): Session
    {
        $loggedUserFilters = User::loggedUserFilters();

        return Session::query()
            ->when($loggedUserFilters[Session::USER_ID] ?? null, function (Builder $query, $value) {
                $query->where(Session::USER_ID, '=', $value);
            })
            ->findOrFail($id);
    }

    public static function defautFiltersQuery(mixed $filters = []): Builder
    {
        $loggedUserFilters = User::loggedUserFilters();

        return Session::query()
            ->when($loggedUserFilters[Session::USER_ID] ?? null, function (Builder $query, $value) {
                $query->where(Session::USER_ID, '=', $value);
            })
            ->when($filters[Session::NAME] ?? null, function (Builder $query, $value) {
                $query->where(Session::NAME, 'like', "%$value%");
            });
    }
}

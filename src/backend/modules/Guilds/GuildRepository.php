<?php

namespace Guilds;

use Illuminate\Database\Eloquent\Builder;
use Users\User;

/**
 * Class GuildRepository
 * Build commom module queries here
 */
class GuildRepository
{
    public static function findOrFail(int $id): Guild
    {
        $loggedUserFilters = User::loggedUserFilters();

        return Guild::query()
            ->when($loggedUserFilters[Guild::USER_ID] ?? null, function (Builder $query, $value) {
                $query->where(Guild::USER_ID, '=', $value);
            })
            ->findOrFail($id);
    }

    public static function defautFiltersQuery(mixed $filters = []): Builder
    {
        return Guild::query();
        //    ->when($filters[Guild::NAME] ?? null, function (Builder $query, $value) {
        //        $query->where(Guild::NAME, 'like', "%$value%");
        //    });
    }
}

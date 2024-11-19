<?php

namespace Players;

use Illuminate\Database\Eloquent\Builder;
use Users\User;

/**
 * Class PlayerRepository
 * Build commom module queries here
 */
class PlayerRepository
{
    public static function findOrFail(int $id): Player
    {
        $loggedUserFilters = User::loggedUserFilters();

        return Player::query()
            ->when($loggedUserFilters[Player::USER_ID] ?? null, function (Builder $query, $value) {
                $query->where(Player::USER_ID, '=', $value);
            })
            ->findOrFail($id);
    }

    public static function defautFiltersQuery(mixed $filters = []): Builder
    {
        $loggedUserFilters = User::loggedUserFilters();

        return Player::query()
            ->when($loggedUserFilters[Player::USER_ID] ?? null, function (Builder $query, $value) {
                $query->where(Player::USER_ID, '=', $value);
            })
            ->when($filters[Player::NAME] ?? null, function (Builder $query, $value) {
                $query->where(Player::NAME, 'like', "%$value%");
            })
            ->when(is_bool($filters[Player::ACTIVE] ?? null), function (Builder $query) use ($filters) {
                $query->where(Player::ACTIVE, $filters[Player::ACTIVE]);
            });
    }
}

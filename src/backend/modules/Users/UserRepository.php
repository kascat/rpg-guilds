<?php

namespace Users;

use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    public static function findOrFail(int $id): User
    {
        $loggedUserFilters = User::loggedUserFilters();

        return User::query()
             ->when($loggedUserFilters[User::STORE_ID] ?? null, function (Builder $query, $value) {
                 $query->where(User::STORE_ID, '=', $value);
             })
            ->when($loggedUserFilters[User::SALES_POINT_ID] ?? null, function (Builder $query, $value) {
                 $query->where(User::SALES_POINT_ID, '=', $value);
             })
            ->findOrFail($id);
    }

    public static function searchFromEmail(string $email): Builder
    {
        return User::query()->whereEmail($email);
    }

    public static function defautFiltersQuery($filters = [])
    {
        $filters = array_merge($filters, User::loggedUserFilters());

        return User::query()
            ->when($filters[User::NAME] ?? null, function ($query, $value) {
                return $query->where(User::NAME, 'like', "%$value%");
            })->when($filters[User::EMAIL] ?? null, function ($query, $value) {
                return $query->where(User::EMAIL, 'like', "%$value%");
            })->when($filters[User::ROLE] ?? null, function ($query, $value) {
                return $query->where(User::ROLE, $value);
            })->when($filters[User::PERMISSION_ID] ?? null, function ($query, $permissionId) {
                return $query->where(User::PERMISSION_ID, $permissionId);
            })->when($filters[User::STORE_ID] ?? null, function (Builder $query, $value) {
                return $query->where(User::STORE_ID, $value);
            })->when($filters[User::SALES_POINT_ID] ?? null, function (Builder $query, $value) {
                return $query->where(User::SALES_POINT_ID, $value);
            });
    }
}
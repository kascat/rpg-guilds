<?php

namespace Permissions\Enums;

enum AbilitiesEnum: string
{
    // Restrict abilities
    case RESET_PASSWORD = 'reset_password';

    // General abilities
    case USERS = 'users';
    case PERMISSIONS = 'permissions';
    case MANAGE_PLAYERS = 'manage_players';
    case MANAGE_SESSIONS = 'manage_sessions';

    public static function availableAbilities(): array
    {
        return [
            self::USERS,
            self::PERMISSIONS,
            self::MANAGE_PLAYERS,
            self::MANAGE_SESSIONS,
        ];
    }

    public static function requireAllAbilities(array $abilities): string
    {
        $abilities = array_map(fn (self $ability) => $ability->value, $abilities);

        return 'abilities:' . implode(',', $abilities);
    }

    public static function requireAnyAbility(array $abilities): string
    {
        $abilities = array_map(fn (self $ability) => $ability->value, $abilities);

        return 'ability:' . implode(',', $abilities);
    }
}

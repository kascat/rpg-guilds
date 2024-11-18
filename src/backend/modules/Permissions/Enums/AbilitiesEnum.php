<?php

namespace Permissions\Enums;

enum AbilitiesEnum: string
{
    // Restrict abilities
    case RESET_PASSWORD = 'reset_password';

    // General abilities
    case USERS = 'users';
    case PERMISSIONS = 'permissions';
    case MANAGE_STORES = 'manage_stores';
    case MANAGE_PAYMENTS = 'manage_payments';
    case MANAGE_PROMOTIONS = 'manage_promotions';
    case PANEL = 'panel';
    case CARD_REPORT = 'card_report';
    case MANAGE_ACCOUNT = 'manage_account';

    public static function availableAbilities(): array
    {
        return [
            self::USERS,
            self::PERMISSIONS,
            self::MANAGE_STORES,
            self::MANAGE_PAYMENTS,
            self::MANAGE_PROMOTIONS,
            self::PANEL,
            self::CARD_REPORT,
            self::MANAGE_ACCOUNT,
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

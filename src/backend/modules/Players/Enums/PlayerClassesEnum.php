<?php

namespace Players\Enums;

enum PlayerClassesEnum: string
{
    case WARRIOR = 'warrior';
    case MAGE = 'mage';
    case ARCHER = 'archer';
    case CLERIC = 'cleric';

    public static function availableClasses(): array
    {
        return [
            self::WARRIOR,
            self::MAGE,
            self::ARCHER,
            self::CLERIC,
        ];
    }
}

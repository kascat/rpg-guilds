<?php

namespace Guilds;

use Guilds\Enums\BalancingStrategyEnum;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Players\Player;

/**
 * Class GuildRequest
 */
class GuildRequest extends Request
{
    public function validateToOrganizeGuilds(): array
    {
        return [
            'players_per_guild' => ['required', 'int'],
            'players' => ['required', 'array', Rule::exists(Player::TABLE, Player::ID)],
            'strategy' => ['required', Rule::enum(BalancingStrategyEnum::class)],
        ];
    }

    public function validateToIndex(): array
    {
        return [
            // Allowed properties to filter, ex:
            // Guild::NAME => 'string',
        ];
    }

    public function validateToStore(): array
    {
        return [
            // Property rules to store, ex:
            // Guild::NAME => 'required|string',
        ];
    }

    public function validateToUpdate(): array
    {
        return [
            // Property rules to update, ex:
            // Guild::NAME => 'string',
        ];
    }
}

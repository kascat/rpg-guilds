<?php

namespace Sessions;

use Guilds\Guild;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Players\Player;

/**
 * Class SessionRequest
 */
class SessionRequest extends Request
{
    public function validateToIndex(): array
    {
        return [
            Session::NAME => ['nullable', 'string'],
        ];
    }

    public function validateToStore(): array
    {
        return [
            Session::NAME => ['required', 'string'],
            Session::RELATION_GUILDS => ['required', 'array'],
            Session::RELATION_GUILDS . '.*.' . Guild::RELATION_PLAYERS => ['required', 'array', Rule::exists(Player::TABLE, Player::ID)],
        ];
    }

    public function validateToUpdate(): array
    {
        return [
            // Property rules to update, ex:
            // Session::NAME => 'string',
        ];
    }
}

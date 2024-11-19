<?php

namespace Players;

use App\Utils\Parse;
use Illuminate\Validation\Rule;
use Kascat\EasyModule\Core\Request;
use Players\Enums\PlayerClassesEnum;

/**
 * Class PlayerRequest
 */
class PlayerRequest extends Request
{
    public function validateToIndex(): array
    {
        return [
            Player::NAME => ['string', 'nullable'],
            Player::ACTIVE => ['boolean', 'nullable'],
        ];
    }

    public function validateToStore(): array
    {
        return [
            Player::NAME => ['required', 'string'],
            Player::PLAYER_CLASS => ['required', Rule::enum(PlayerClassesEnum::class)],
            Player::XP => ['required', 'int', 'min:1', 'max:100'],
            Player::ACTIVE => ['required', 'boolean'],
        ];
    }

    public function validateToUpdate(): array
    {
        return [
            Player::NAME => ['required', 'string'],
            Player::PLAYER_CLASS => ['required', Rule::enum(PlayerClassesEnum::class)],
            Player::XP => ['required', 'int', 'min:1', 'max:100'],
            Player::ACTIVE => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        if ($this->input(Player::ACTIVE)) {
            $data[Player::ACTIVE] = Parse::parseBoolean($this->input(Player::ACTIVE));
        }

        $this->merge($data);
    }
}

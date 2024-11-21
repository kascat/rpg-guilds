<?php

namespace Guilds\Actions;

use Illuminate\Database\Eloquent\Collection;
use Players\Enums\PlayerClassesEnum;
use Players\Player;
use Players\PlayerRepository;

class OrganizeGuildsAction
{
    public function handle(int $playersPerGuild, array $players): array
    {
        $allPlayersQuantity = PlayerRepository::getPlayersSortedByXP($players)->count();
        $guildsQuantity = (int) ceil($allPlayersQuantity / $playersPerGuild);

        $attackingPlayers = PlayerRepository::getPlayersSortedByXP($players, [Player::PLAYER_CLASS => [PlayerClassesEnum::ARCHER, PlayerClassesEnum::MAGE]])->get();
        $suportPlayers = PlayerRepository::getPlayersSortedByXP($players, [Player::PLAYER_CLASS => PlayerClassesEnum::CLERIC])->get();
        $defensivePlayers = PlayerRepository::getPlayersSortedByXP($players, [Player::PLAYER_CLASS => PlayerClassesEnum::WARRIOR])->get();

        $guilds = array_fill(0, $guildsQuantity, ['xp' => 0, 'players' => []]);

        $this->sortPlayersInGuilds($guilds, $attackingPlayers);
        $this->sortPlayersInGuilds($guilds, $defensivePlayers);
        $this->sortPlayersInGuilds($guilds, $suportPlayers);

        return $guilds;
    }

    private function sortPlayersInGuilds(array &$guilds, Collection $players): void
    {
        /** @var Player $player */
        foreach ($players as $player) {
            $minXPIndex = array_reduce(array_keys($guilds), function ($carry, $index) use ($guilds) {
                return ($carry === null || +$guilds[$index]['xp'] < +$guilds[$carry]['xp']) ? $index : $carry;
            });

            $guilds[$minXPIndex]['xp'] += $player->xp;
            $guilds[$minXPIndex]['players'][] = $player->toArray();
        }
    }
}

<?php

namespace Guilds\Actions;

use Guilds\Enums\BalancingStrategyEnum;
use Illuminate\Database\Eloquent\Collection;
use Players\Enums\PlayerClassesEnum;
use Players\Player;
use Players\PlayerRepository;

class OrganizeGuildsAction
{
    public function handle(int $playersPerGuild, array $playerIds, $strategy): array
    {
        $allPlayersQuantity = PlayerRepository::getPlayersSortedByXP($playerIds)->count();
        $guildsQuantity = (int) ceil($allPlayersQuantity / $playersPerGuild);

        $guilds = array_fill(0, $guildsQuantity, ['xp' => 0, 'players' => []]);

        return match ($strategy) {
            BalancingStrategyEnum::XP->value => $this->balancingByXp($guilds, $playerIds),
            BalancingStrategyEnum::PLAYER_CLASS->value => $this->balancingByClass($guilds, $playerIds),
        };
    }

    private function balancingByXp(array $guilds, array $playerIds): array
    {
        $allPlayers = PlayerRepository::getPlayersSortedByXP($playerIds)->get();

        $this->sortPlayersInGuilds($guilds, $allPlayers);

        return $guilds;
    }

    private function balancingByClass(array $guilds, array $playerIds): array
    {
        $attackingPlayers = PlayerRepository::getPlayersSortedByXP($playerIds, [Player::PLAYER_CLASS => [PlayerClassesEnum::ARCHER, PlayerClassesEnum::MAGE]])->get();
        $suportPlayers = PlayerRepository::getPlayersSortedByXP($playerIds, [Player::PLAYER_CLASS => PlayerClassesEnum::CLERIC])->get();
        $defensivePlayers = PlayerRepository::getPlayersSortedByXP($playerIds, [Player::PLAYER_CLASS => PlayerClassesEnum::WARRIOR])->get();

        $this->sortPlayersInGuilds($guilds, $suportPlayers);
        $this->sortPlayersInGuilds($guilds, $defensivePlayers);
        $this->sortPlayersInGuilds($guilds, $attackingPlayers);

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

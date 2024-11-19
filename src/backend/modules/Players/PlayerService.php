<?php

namespace Players;

use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Auth;
use Kascat\EasyModule\Core\Service;
use Users\User;

/**
 * Class PlayerService
 */
class PlayerService extends Service
{
    public function index(array $filters): array
    {
        $playersQuery = PlayerRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $playersQuery
                ->with(\request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(\request(self::PER_PAGE))
        );
    }

    public function show(Player $player): array
    {
        return self::buildReturn(
            $player
                ->load(\request(self::WITH_RELATIONSHIP) ?? [])
                ->toArray()
        );
    }

    public function store(array $data): array
    {
        /** @var User|null $loggedUser */
        $loggedUser = Auth::guard(AuthServiceProvider::GUARD_USER)->user();

        $data[Player::USER_ID] = $loggedUser->id;

        $player = Player::query()->create($data);

        return self::buildReturn($player);
    }

    public function update(Player $player, array $data): array
    {
        $player->update($data);

        return self::buildReturn($player);
    }

    public function destroy(Player $player): array
    {
        $player->delete();

        return self::buildReturn();
    }
}

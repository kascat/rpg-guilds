<?php

namespace Guilds;

use Guilds\Actions\OrganizeGuildsAction;
use Illuminate\Support\Facades\Log;
use Kascat\EasyModule\Core\Service;
use Throwable;

/**
 * Class GuildService
 */
class GuildService extends Service
{
    public function __construct(private readonly OrganizeGuildsAction $organizeGuildsAction)
    {
    }

    public function organizeGuilds(array $data): array
    {
        try {
            $organizedGuilds = $this->organizeGuildsAction->handle(
                $data['players_per_guild'],
                $data['players'],
                $data['strategy']
            );

            return self::buildReturn($organizedGuilds);
        } catch (Throwable $exception) {
            Log::error('GuildService: Error on organize guilds', [
                'errorMessage' => $exception->getMessage()
            ]);

            return self::buildReturn([], 422);
        }
    }

    public function index(array $filters): array
    {
        $guildsQuery = GuildRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $guildsQuery
                ->with(\request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(\request(self::PER_PAGE))
        );
    }

    public function show(Guild $guild): array
    {
        return self::buildReturn(
            $guild
                ->load(\request(self::WITH_RELATIONSHIP) ?? [])
                ->toArray()
        );
    }

    public function store(array $data): array
    {
        $guild = Guild::query()->create($data);

        return self::buildReturn($guild);
    }

    public function update(Guild $guild, array $data): array
    {
        $guild->update($data);

        return self::buildReturn($guild);
    }

    public function destroy(Guild $guild): array
    {
        $guild->delete();

        return self::buildReturn();
    }
}

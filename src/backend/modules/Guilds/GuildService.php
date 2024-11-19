<?php

namespace Guilds;

use Kascat\EasyModule\Core\Service;

/**
 * Class GuildService
 */
class GuildService extends Service
{
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

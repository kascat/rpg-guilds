<?php

namespace Guilds;

use App\Http\Controllers\Controller;

/**
 * Class GuildController
 */
class GuildController extends Controller
{
    use GuildResponse;

    public function __construct(private readonly GuildService $guildService)
    {
        //
    }

    public function organizeGuilds(GuildRequest $request): mixed
    {
        $result = $this->guildService->organizeGuilds($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function index(GuildRequest $request): mixed
    {
        $result = $this->guildService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function store(GuildRequest $request): mixed
    {
        $result = $this->guildService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function show(int $id): mixed
    {
        $guild = GuildRepository::findOrFail($id);

        $result = $this->guildService->show($guild);

        return $this->response($result['response'], $result['status']);
    }

    public function update(GuildRequest $request, int $id): mixed
    {
        $guild = GuildRepository::findOrFail($id);

        $result = $this->guildService->update($guild, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function destroy(int $id): mixed
    {
        $guild = GuildRepository::findOrFail($id);

        $result = $this->guildService->destroy($guild);

        return $this->response($result['response'], $result['status']);
    }
}

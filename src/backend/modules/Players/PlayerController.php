<?php

namespace Players;

use App\Http\Controllers\Controller;

/**
 * Class PlayerController
 */
class PlayerController extends Controller
{
    use PlayerResponse;

    public function __construct(private readonly PlayerService $playerService)
    {
        //
    }

    public function index(PlayerRequest $request): mixed
    {
        $result = $this->playerService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function store(PlayerRequest $request): mixed
    {
        $result = $this->playerService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function show(int $id): mixed
    {
        $player = PlayerRepository::findOrFail($id);

        $result = $this->playerService->show($player);

        return $this->response($result['response'], $result['status']);
    }

    public function update(PlayerRequest $request, int $id): mixed
    {
        $player = PlayerRepository::findOrFail($id);

        $result = $this->playerService->update($player, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function destroy(int $id): mixed
    {
        $player = PlayerRepository::findOrFail($id);

        $result = $this->playerService->destroy($player);

        return $this->response($result['response'], $result['status']);
    }
}

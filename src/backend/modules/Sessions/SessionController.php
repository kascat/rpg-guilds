<?php

namespace Sessions;

use App\Http\Controllers\Controller;

/**
 * Class SessionController
 */
class SessionController extends Controller
{
    use SessionResponse;

    public function __construct(private readonly SessionService $sessionService)
    {
        //
    }

    public function index(SessionRequest $request): mixed
    {
        $result = $this->sessionService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function store(SessionRequest $request): mixed
    {
        $result = $this->sessionService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function show(int $id): mixed
    {
        $session = SessionRepository::findOrFail($id);

        $result = $this->sessionService->show($session);

        return $this->response($result['response'], $result['status']);
    }

    public function update(SessionRequest $request, int $id): mixed
    {
        $session = SessionRepository::findOrFail($id);

        $result = $this->sessionService->update($session, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    public function destroy(int $id): mixed
    {
        $session = SessionRepository::findOrFail($id);

        $result = $this->sessionService->destroy($session);

        return $this->response($result['response'], $result['status']);
    }
}

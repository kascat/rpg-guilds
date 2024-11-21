<?php

namespace Sessions;

use App\Providers\AuthServiceProvider;
use Guilds\Guild;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kascat\EasyModule\Core\Service;
use Users\User;
use Throwable;

/**
 * Class SessionService
 */
class SessionService extends Service
{
    public function index(array $filters): array
    {
        $sessionsQuery = SessionRepository::defautFiltersQuery($filters);

        return self::buildReturn(
            $sessionsQuery
                ->with(\request(self::WITH_RELATIONSHIP) ?? [])
                ->paginate(\request(self::PER_PAGE))
        );
    }

    public function show(Session $session): array
    {
        return self::buildReturn(
            $session
                ->load(\request(self::WITH_RELATIONSHIP) ?? [])
                ->toArray()
        );
    }

    public function store(array $data): array
    {
        /** @var User|null $loggedUser */
        $loggedUser = Auth::guard(AuthServiceProvider::GUARD_USER)->user();

        DB::beginTransaction();

        try {
            /** @var Session $session */
            $session = Session::query()->create([
                Session::USER_ID => $loggedUser->id,
                Session::NAME => $data[Session::NAME],
            ]);

            foreach ($data[Session::RELATION_GUILDS] as $guildData) {
                /** @var Guild $guild */
                $guild = $session->guilds()->create([
                    Guild::USER_ID => $session->user_id,
                ]);

                $guild->players()->sync($guildData[Guild::RELATION_PLAYERS]);
            }

            DB::commit();

            $session->load(Session::RELATION_GUILDS . '.' . Guild::RELATION_PLAYERS);

            return self::buildReturn($session);
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('SessionService: Error on save Session', [
                'errorMessage' => $exception->getMessage(),
            ]);

            return self::buildReturn(['message' => 'Falha ao salvar sessão'], 422);
        }
    }

    public function update(Session $session, array $data): array
    {
        $session->update($data);

        return self::buildReturn($session);
    }

    public function destroy(Session $session): array
    {
        DB::beginTransaction();

        try {
            $session->guilds->each(function (Guild $guild) {
                $guild->players()->detach();
                $guild->delete();
            });

            $session->delete();

            DB::commit();

            return self::buildReturn();
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('SessionService: Error on delete', [
                'errorMessage' => $exception->getMessage(),
            ]);

            return self::buildReturn(['message' => 'Falha ao remover sessão'], 422);
        }

    }
}

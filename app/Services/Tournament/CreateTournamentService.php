<?php
declare(strict_types = 1);

namespace App\Services\Tournament;

use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use App\Services\ExecResult;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CreateTournamentService
 * @package App\Services\Tournament
 */
class CreateTournamentService
{
    /**
     * @param User|Authenticatable $creator
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function create(User $creator, array $data): ExecResult
    {
        DB::beginTransaction();
        try {
            /** @var Tournament $tournament */
            $tournament = Tournament::create(array_merge($data, ['creator_uuid' => $creator->uuid]));

            foreach ($data['players'] as $index => $player) {
                TournamentPlayer::create(array_merge($player, [
                    'tournament_uuid' => $tournament->uuid,
                    'order' => $index,
                ]));
            }

            DB::commit();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            DB::rollBack();

            return ExecResult::instance()
                ->setMessage($e->getMessage());
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData($tournament->refresh()->toArray());
    }
}

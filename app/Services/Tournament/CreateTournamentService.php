<?php
declare(strict_types = 1);

namespace App\Services\Tournament;

use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use App\Services\ExecResult;
use App\Services\File\FileUploadService;
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
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * CreateTournamentService constructor.
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

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

            if (isset($data['image'])) {
                $this->fileUploadService->upload('tournament/' . $tournament->uuid, $data['image'], $tournament);
            }

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
            ->setData(array_merge(
                $tournament->refresh()->toArray(),
                ['images' => $tournament->images]
            ));
    }
}

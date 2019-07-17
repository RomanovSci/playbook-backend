<?php
declare(strict_type = 1);

namespace App\Services\Tournament;

use App\Models\TournamentParticipant;
use App\Models\TournamentRequest;
use App\Services\ExecResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ApproveTournamentRequestService
 * @package App\Services\Tournament
 */
class ApproveTournamentRequestService
{
    /**
     * @var ChallongeAPI
     */
    protected $challongeAPI;

    /**
     * @var TournamentRequest
     */
    protected $tournamentRequest;

    /**
     * ApproveTournamentRequestService constructor.
     * @param ChallongeAPI $challongeAPI
     */
    public function __construct(ChallongeAPI $challongeAPI)
    {
        $this->challongeAPI = $challongeAPI;
    }

    /**
     * Approve tournament request
     *
     * @param TournamentRequest $tournamentRequest
     * @return ExecResult
     */
    public function approve(TournamentRequest $tournamentRequest): ExecResult
    {
        $this->tournamentRequest = $tournamentRequest;
        DB::beginTransaction();

        try {
            $tournamentRequest->approved_at = Carbon::now()->format('Y-m-d H:i:s');
            $tournamentRequest->update(['approved_at']);
            $tournamentParticipant = $this->createInternalParticipant(
                $this->createExternalParticipant()
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ExecResult::instance()->setMessage($e->getMessage());
        }

        DB::commit();
        return ExecResult::instance()
            ->setSuccess()
            ->setData($tournamentParticipant->toArray());
    }

    /**
     * Create participant at challonge system
     *
     * @return int
     * @throws \Exception
     */
    protected function createExternalParticipant(): int
    {
        $createResult = $this->challongeAPI->createParticipant($this->tournamentRequest->tournament->challonge_id, [
            'participant' => [
                'name' => $this->tournamentRequest->user_uuid,
            ]
        ]);

        if ($createResult['status_code'] !== 200) {
            throw new \Exception(
                "Can't create challonge participant. Reason: " . $createResult['errors'][0]
            );
        }

        return (int) $createResult['data']->participant->id;
    }

    /**
     * Create participant at our system
     *
     * @param int $challongeId
     * @return TournamentParticipant
     */
    protected function createInternalParticipant(int $challongeId): TournamentParticipant
    {
        return TournamentParticipant::create([
            'nickname' => $this->tournamentRequest->user_uuid,
            'user_uuid' => $this->tournamentRequest->user_uuid,
            'tournament_uuid' => $this->tournamentRequest->tournament_uuid,
            'challonge_id' => $challongeId,
        ]);
    }
}

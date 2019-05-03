<?php

namespace App\Services;

use App\Models\Tournament;
use App\Services\Tournament\ChallongeAPI;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StartTournamentService
 * @package App\Services
 */
class StartTournamentService
{
    /**
     * @var ChallongeAPI
     */
    protected $challongeAPI;

    /**
     * StartTournamentService constructor.
     * @param ChallongeAPI $challongeAPI
     */
    public function __construct(ChallongeAPI $challongeAPI)
    {
        $this->challongeAPI = $challongeAPI;
    }

    /**
     * @param Tournament $tournament
     * @return ExecResult
     */
    public function start(Tournament $tournament): ExecResult
    {
        DB::beginTransaction();

        try {
            $tournament->started_at = Carbon::now()->format('Y-m-d H:i:s');
            $tournament->update(['started_at']);

            $result = $this->challongeAPI->startTournament($tournament->challonge_id);

            if ($result['status_code'] !== 200) {
                throw new \Exception(
                    "Can't start tournament. Reason: " . $result['errors'][0]
                );
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return ExecResult::instance()->setMessage($e->getMessage());
        }

        DB::commit();
        return ExecResult::instance()
            ->setSuccess()
            ->setData($tournament->toArray());
    }
}

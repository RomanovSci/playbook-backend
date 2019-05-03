<?php

namespace App\Services;

use App\Models\Tournament;
use App\Services\Tournament\ChallongeAPI;

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
        return ExecResult::instance()->setSuccess();
    }
}

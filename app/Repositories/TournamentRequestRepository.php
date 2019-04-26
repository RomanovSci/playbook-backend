<?php

namespace App\Repositories;

use App\Models\TournamentRequest;

/**
 * Class TournamentRequestRepository
 * @package App\Repositories
 */
class TournamentRequestRepository
{
    /**
     * Get one by uuid
     *
     * @param string $uuid
     * @return TournamentRequest|null
     */
    public static function getByUuid(string $uuid): ?TournamentRequest
    {
        return TournamentRequest::where('uuid', $uuid)->first();
    }

    /**
     * Get tournament request by tournament and user uuids
     *
     * @param string $tournamentUuid
     * @param string $userUuid
     * @return TournamentRequest|null
     */
    public static function getByTournamentAndUserUuid(string $tournamentUuid, string $userUuid): ?TournamentRequest
    {
        return TournamentRequest::where('tournament_uuid', $tournamentUuid)
            ->where('user_uuid', $userUuid)
            ->first();
    }
}

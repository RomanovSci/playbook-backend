<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\TournamentInvitation;

/**
 * Class TournamentInvitationRepository
 * @package App\Repositories
 */
class TournamentInvitationRepository
{
    /**
     * @param string $uuid
     * @return TournamentInvitation|null
     */
    public static function getByUuid(string $uuid): ?TournamentInvitation
    {
        return TournamentInvitation::where('uuid', $uuid)->first();
    }

    /**
     * @param string $tournamentUuid
     * @param string $invitedUuid
     * @return TournamentInvitation|null
     */
    public static function getByTournamentAndInvitedUuid(
        string $tournamentUuid,
        string $invitedUuid
    ): ?TournamentInvitation {
        return TournamentInvitation::where('invited_uuid', $invitedUuid)
            ->where('tournament_uuid', $tournamentUuid)
            ->first();
    }
}

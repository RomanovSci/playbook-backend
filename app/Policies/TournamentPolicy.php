<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

/**
 * Class TournamentPolicy
 * @package App\Policies
 */
class TournamentPolicy
{
    /**
     * @param User $user
     * @param Tournament $tournament
     * @return bool
     */
    public function startTournament(User $user, Tournament $tournament): bool
    {
        return $tournament->creator_uuid === $user->uuid;
    }
}

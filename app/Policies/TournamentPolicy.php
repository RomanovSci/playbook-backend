<?php

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
     * Determine if the user can invite another user into tournament
     *
     * @param User $user
     * @param Tournament $tournament
     * @return bool
     */
    public function invite(User $user, Tournament $tournament): bool
    {
        return $user->uuid === $tournament->creator_uuid;
    }
}

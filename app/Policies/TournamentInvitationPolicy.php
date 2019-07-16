<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\TournamentInvitation;
use App\Models\User;

class TournamentInvitationPolicy
{
    /**
     * Determine if the user can approve tournament invitation
     *
     * @param User $user
     * @param TournamentInvitation $tournamentInvitation
     * @return bool
     */
    public function approve(User $user, TournamentInvitation $tournamentInvitation): bool
    {
        return $user->uuid === $tournamentInvitation->invited_uuid;
    }
}

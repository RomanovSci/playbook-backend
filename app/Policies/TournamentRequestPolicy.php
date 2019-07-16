<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\TournamentRequest;
use App\Models\User;

/**
 * Class TournamentRequestPolicy
 * @package App\Policies
 */
class TournamentRequestPolicy
{
    /**
     * Determine if the user can approve tournament request
     *
     * @param User $user
     * @param TournamentRequest $tournamentRequest
     * @return bool
     */
    public function approve(User $user, TournamentRequest $tournamentRequest): bool
    {
        return $user->uuid === $tournamentRequest->tournament->creator_uuid;
    }
}

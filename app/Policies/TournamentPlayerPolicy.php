<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\TournamentPlayer;
use App\Models\User;

/**
 * Class TournamentPlayerPolicy
 * @package App\Policies
 */
class TournamentPlayerPolicy
{
    /**
     * @param User $user
     * @param TournamentPlayer $tournamentPlayer
     * @return bool
     */
    public function deletePlayer(User $user, TournamentPlayer $tournamentPlayer): bool
    {
        return $user->uuid === $tournamentPlayer->tournament->creator_uuid;
    }
}

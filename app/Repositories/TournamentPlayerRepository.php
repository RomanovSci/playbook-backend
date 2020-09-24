<?php
declare(strict_types = 1);

namespace App\Repositories;
use App\Models\TournamentPlayer;

/**
 * Class TournamentPlayerRepository
 * @package App\Repositories
 */
class TournamentPlayerRepository extends Repository
{
    protected const MODEL = TournamentPlayer::class;
}

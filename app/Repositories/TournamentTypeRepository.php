<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\TournamentType;

/**
 * Class TournamentTypeRepository
 * @package App\Repositories
 */
class TournamentTypeRepository extends Repository
{
    protected const MODEL = TournamentType::class;
}

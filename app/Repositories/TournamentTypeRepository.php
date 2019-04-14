<?php

namespace App\Repositories;

use App\Models\TournamentType;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TournamentTypeRepository
 * @package App\Repositories
 */
class TournamentTypeRepository
{
    /**
     * Get all tournament types
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        return TournamentType::all();
    }
}

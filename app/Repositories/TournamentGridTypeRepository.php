<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\TournamentGridType;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TournamentGridTypeRepository
 * @package App\Repositories
 */
class TournamentGridTypeRepository
{
    /**
     * Get all tournament grid types
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        return TournamentGridType::all();
    }
}

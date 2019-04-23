<?php

namespace App\Repositories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TournamentRepository
 * @package App\Repositories
 */
class TournamentRepository
{
    /**
     * Get all tournaments
     *
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return Tournament::limit($limit)->offset($offset)->get();
    }
}

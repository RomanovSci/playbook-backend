<?php

namespace App\Repositories;

use App\Models\Playground;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PlaygroundRepository
 * @package App\Repositories
 */
class PlaygroundRepository
{
    /**
     * Get all playgrounds
     *
     * @return Collection
     */
    public static function getAll(): Collection
    {
        $playgrounds = Playground::all();
        return $playgrounds;
    }

    /**
     * Search playgrounds
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        $playgrounds = Playground::where('name', 'ilike', "%$query%")
            ->orWhere('description', 'ilike', "%$query%")
            ->orWhere('address', 'ilike', "%$query%")
            ->get();

        return $playgrounds;
    }
}

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

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
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return Playground::limit($limit)->offset($offset)->get();
    }

    /**
     * Search playgrounds
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        return Playground::where('name', 'ilike', "%$query%")
            ->orWhere('description', 'ilike', "%$query%")
            ->orWhere('address', 'ilike', "%$query%")
            ->get();
    }
}

<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CityRepository
 * @package App\Repositories
 */
class CityRepository
{
    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return City::limit($limit)->offset($offset)->get();
    }

    /**
     * Search cities
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        return City::where('name', 'ilike', "%$query%")->get();
    }
}

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
        $cities = City::limit($limit)->offset($offset)->get();
        return $cities;
    }

    /**
     * Search cities
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        $cities = City::where('name', 'ilike', "%$query%")->get();
        return $cities;
    }
}

<?php

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CountryRepository
 * @package App\Repositories
 */
class CountryRepository
{
    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return Country::limit($limit)->offset($offset)->get();
    }

    /**
     * Search cities
     *
     * @param string $query
     * @return Collection
     */
    public static function search(string $query): Collection
    {
        return Country::where('name', 'ilike', "%$query%")->get();
    }
}

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
        return self::query($limit, $offset)->get();
    }

    /**
     * Search cities
     *
     * @param array $data
     * @return Collection
     */
    public static function search(array $data): Collection
    {
        return self::query($data['limit'], $data['offset'])
            ->where('name', 'ilike', '%' . $data['query'] . '%')
            ->get();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    protected static function query(int $limit, int $offset)
    {
        return City::limit($limit)->offset($offset);
    }
}

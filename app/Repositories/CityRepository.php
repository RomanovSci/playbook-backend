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
     * @param array $data
     * @return Collection
     */
    public static function get(array $data): Collection
    {
        $query = self::query($data['limit'], $data['offset']);

        if (isset($data['country_uuid'])) {
            $query->where('country_uuid', $data['country_uuid']);
        }

        return $query->get();
    }

    /**
     * Search cities
     *
     * @param array $data
     * @return Collection
     */
    public static function search(array $data): Collection
    {
        $query = self::query($data['limit'], $data['offset'])
            ->where('name', 'ilike', '%' . $data['query'] . '%');

        if (isset($data['country_uuid'])) {
            $query->where('country_uuid', $data['country_uuid']);
        }

        return $query->get();
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

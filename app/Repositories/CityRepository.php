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
        $query = City::limit($data['limit'])->offset($data['offset']);

        if (isset($data['country_uuid'])) {
            $query->where('country_uuid', $data['country_uuid']);
        }

        if (isset($data['query'])) {
            $query->where('name', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->get();
    }
}

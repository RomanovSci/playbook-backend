<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CityRepository
 * @package App\Repositories
 */
class CityRepository extends Repository
{
    protected const MODEL = City::class;

    /**
     * @param array $data
     * @return Collection
     */
    public function get(array $data): Collection
    {
        $query = $this->builder()->limit($data['limit'])->offset($data['offset']);

        if (isset($data['country_uuid'])) {
            $query->where('country_uuid', $data['country_uuid']);
        }

        if (isset($data['query'])) {
            $query->where('name', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->get();
    }
}

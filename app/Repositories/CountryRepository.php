<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CountryRepository
 * @package App\Repositories
 */
class CountryRepository extends Repository
{
    protected const MODEL = Country::class;

    /**
     * @param array $data
     * @return Collection
     */
    public function get(array $data): Collection
    {
        $query = $this->builder()->limit($data['limit'])->offset($data['offset']);

        if (isset($data['query'])) {
            $query->where('name', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->get();
    }
}

<?php
declare(strict_types = 1);

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
     * @param array $data
     * @return Collection
     */
    public static function get(array $data): Collection
    {
        $query = Country::limit($data['limit'])->offset($data['offset']);

        if (isset($data['query'])) {
            $query->where('name', 'ilike', '%' . $data['query'] . '%');
        }

        return $query->get();
    }
}

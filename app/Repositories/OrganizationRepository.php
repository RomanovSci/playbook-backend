<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * Class OrganizationRepository
 * @package App\Repositories
 */
class OrganizationRepository
{
    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public static function get(int $limit, int $offset): Collection
    {
        return Organization::limit($limit)
            ->offset($offset)
            ->get();
    }
}

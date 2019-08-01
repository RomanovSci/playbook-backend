<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * Class OrganizationRepository
 * @package App\Repositories
 */
class OrganizationRepository extends Repository
{
    protected const MODEL = Organization::class;

    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function get(int $limit, int $offset): Collection
    {
        return $this->builder()->limit($limit)->offset($offset)->get();
    }
}

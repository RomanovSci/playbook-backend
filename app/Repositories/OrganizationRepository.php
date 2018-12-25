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
     * Get all organizations
     *
     * @return Collection
     */
    public static function getAll(): Collection
    {
        $organizations = Organization::all();
        return $organizations;
    }
}

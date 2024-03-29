<?php
declare(strict_types = 1);

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

/**
 * Class OrganizationPolicy
 * @package App\Policies
 */
class OrganizationPolicy
{
    /**
     * Determine if the playground can be created by user
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function createPlayground(User $user, Organization $organization): bool
    {
        return $user->uuid === $organization->owner_uuid;
    }
}

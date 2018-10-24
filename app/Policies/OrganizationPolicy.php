<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class OrganizationPolicy
 *
 * @package App\Policies
 */
class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the playground can be created by user
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function createPlayground(User $user, Organization $organization)
    {
        return $user->id === $organization->owner_id;
    }
}

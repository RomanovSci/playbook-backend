<?php

namespace App\Policies;

use App\Models\Playground;
use App\Models\User;

/**
 * Class PlaygroundPolicy
 * @package App\Policies
 */
class PlaygroundPolicy
{
    /**
     * Determine if the playground schedule can be created by user
     *
     * @param User $user
     * @param Playground $playground
     * @return bool
     */
    public function createSchedule(User $user, Playground $playground): bool
    {
        return $user->uuid === $playground->organization->owner_uuid;
    }
}

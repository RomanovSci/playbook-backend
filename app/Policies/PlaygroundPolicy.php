<?php

namespace App\Policies;

use App\Models\Playground;
use App\Models\User;

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
        return $user->id === $playground->organization->owner_id;
    }
}

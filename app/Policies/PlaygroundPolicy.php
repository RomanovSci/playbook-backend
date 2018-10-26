<?php

namespace App\Policies;

use App\Models\Playground;
use App\Models\User;

class PlaygroundPolicy
{
    /**
     * Determine if the playground rent price can be created by user
     *
     * @param User $user
     * @param Playground $playground
     * @return bool
     */
    public function createRentPrice(User $user, Playground $playground)
    {
        return $user->id === $playground->organization->owner_id;
    }
}
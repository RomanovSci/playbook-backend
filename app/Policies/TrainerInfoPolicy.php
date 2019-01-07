<?php

namespace App\Policies;

use App\Models\TrainerInfo;
use App\Models\User;

/**
 * Class TrainerInfoPolicy
 * @package App\Policies
 */
class TrainerInfoPolicy
{
    /**
     * Determine if the trainer info can be edit by user
     *
     * @param User $user
     * @param TrainerInfo $info
     * @return bool
     */
    public function edit(User $user, TrainerInfo $info): bool
    {
        return $user->id === $info->user_id;
    }
}

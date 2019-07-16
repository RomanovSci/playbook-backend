<?php
declare(strict_types = 1);

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
        return $user->uuid === $info->user_uuid;
    }
}

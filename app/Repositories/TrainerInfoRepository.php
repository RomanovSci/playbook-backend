<?php

namespace App\Repositories;

use App\Models\TrainerInfo;
use App\Models\User;

/**
 * Class TrainerInfoRepository
 * @package App\Repositories
 */
class TrainerInfoRepository
{
    /**
     * Get trainer info by user uuid
     *
     * @param User $user
     * @return mixed
     */
    public static function getByUser(User $user)
    {
        return TrainerInfo::where('user_uuid', $user->uuid)->with('user')->first();
    }
}

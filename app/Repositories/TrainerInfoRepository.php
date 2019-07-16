<?php
declare(strict_types = 1);

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
     * @return TrainerInfo|null
     */
    public static function getByUser(User $user): ?TrainerInfo
    {
        return TrainerInfo::where('user_uuid', $user->uuid)->with('user')->first();
    }
}

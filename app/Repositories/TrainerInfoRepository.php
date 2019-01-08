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
     * Get trainer info by user id
     *
     * @param User $user
     * @return mixed
     */
    public static function getByUser(User $user)
    {
        return TrainerInfo::where('user_id', $user->id)
            ->with('user')
            ->first();
    }

    /**
     * Get trainer info with playgrounds by user id
     *
     * @param User $user
     * @return array
     */
    public static function getWithPlaygroundsByUser(User $user): array
    {
        /** @var TrainerInfo $trainerInfo */
        $trainerInfo = TrainerInfo::where('user_id', $user->id)
            ->with('user')
            ->first();

        if (!$trainerInfo instanceof TrainerInfo) {
            return [];
        }

        return array_merge($trainerInfo->toArray(), [
            'playgrounds' => $user->playgrounds,
        ]);
    }
}

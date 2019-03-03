<?php

namespace App\Repositories;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class PasswordResetRepository
 * @package App\Repositories
 */
class PasswordResetRepository
{
    /**
     * Get actual reset password entity
     *
     * @param User $user
     * @return mixed
     */
    public static function getActualByUser(User $user)
    {
        return PasswordReset::where('user_uuid', $user->uuid)
            ->where('expired_at', '>', Carbon::now()->toDateTimeString())
            ->where('used_at', null)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

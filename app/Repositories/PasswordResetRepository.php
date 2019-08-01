<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class PasswordResetRepository
 * @package App\Repositories
 */
class PasswordResetRepository extends Repository
{
    protected const MODEL = PasswordReset::class;

    /**
     * Get actual reset password entity
     *
     * @param User $user
     * @return mixed
     */
    public function getActualByUser(User $user): ?PasswordReset
    {
        return $this->builder()
            ->where('user_uuid', $user->uuid)
            ->where('expired_at', '>', Carbon::now()->toDateTimeString())
            ->where('used_at', null)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

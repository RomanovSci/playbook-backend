<?php

namespace App\Services;
use App\Events\User\ResetPasswordEvent;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Carbon\Carbon;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * Reset user password
     *
     * @param User $user
     * @return array
     */
    public function resetPassword(User $user): array
    {
        try {
            $passwordReset = PasswordResetRepository::getActualByUser($user);

            if (!$passwordReset) {
                $passwordReset = PasswordReset::create([
                    'user_uuid' => $user->uuid,
                    'reset_code' => rand(100000, 999999),
                    'expired_at' => Carbon::now()->addHours(3)
                ]);
            }

            if (app()->environment() === 'production') {
                event(new ResetPasswordEvent($passwordReset));
                return ['success' => true];
            }

            return [
                'success' => true,
                'data' => $passwordReset
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

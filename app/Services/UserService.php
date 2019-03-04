<?php

namespace App\Services;
use App\Events\User\RegisterUserEvent;
use App\Events\User\ResetPasswordEvent;
use App\Exceptions\Http\UnauthorizedHttpException;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * Login user
     *
     * @param array $data
     * @return array
     */
    public function loginUser(array $data): array
    {
        /**
         * @var User $user
         * @var PasswordReset $passwordReset
         */
        $user = User::where('phone', $data['phone'])->first();
        $passwordReset = PasswordResetRepository::getActualByUser($user);

        /**
         * Set new password if password
         * request exists and login user
         */
        if ($passwordReset && $passwordReset->reset_code === $data['password']) {
            $user->password = bcrypt($passwordReset->reset_code);
            $user->update(['password']);

            $passwordReset->used_at = Carbon::now();
            $passwordReset->update(['used_at']);
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new UnauthorizedHttpException();
        }

        return [
            'success' => true,
            'data' => array_merge([
                'access_token' => $user->createToken('MyApp')->accessToken,
                'roles' => $user->getRoleNames(),
            ], $user->toArray())
        ];
    }

    /**
     * Register new user
     *
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function registerUser(array $data): array
    {
        $data['verification_code'] = rand(100000, 999999);
        $data['password'] = bcrypt($data['password'] ?? $data['verification_code']);

        DB::beginTransaction();
        try {
            /**
             * @var User $user
             * @var PersonalAccessTokenResult $token
             */
            $user = User::create($data);
            $user->assignRole($data['is_trainer'] ? User::ROLE_TRAINER : User::ROLE_USER);
            $token = $user->createToken('MyApp');

            DB::commit();
            event(new RegisterUserEvent($user));

            return [
                'success' => true,
                'data' => [
                    'access_token' => $token->accessToken,
                    'verification_code' => (app()->environment() === 'production')
                        ? null
                        : $data['verification_code'],
                ],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset user password
     *
     * @param User $user
     * @return array
     */
    public function resetUserPassword(User $user): array
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
            event(new ResetPasswordEvent($passwordReset));

            return [
                'success' => true,
                'data' => (app()->environment() === 'production')
                    ? null
                    : $passwordReset
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

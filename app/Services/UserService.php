<?php

namespace App\Services;

use App\Exceptions\Http\UnauthorizedHttpException;
use App\Jobs\SendSms;
use App\Models\PasswordReset;
use App\Models\User;
use App\Objects\Service\ExecResult;
use App\Repositories\PasswordResetRepository;
use App\Repositories\TimezoneRepository;
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
     * @return ExecResult
     */
    public static function login(array $data): ExecResult
    {
        /** @var User $user */
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            throw new UnauthorizedHttpException();
        }

        /** @var PasswordReset $passwordReset */
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

        if (!Hash::check($data['password'], $user->password)) {
            throw new UnauthorizedHttpException();
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData(array_merge([
                'access_token' => $user->createToken('MyApp')->accessToken,
                'roles' => $user->getRoleNames(),
            ], $user->toArray()));
    }

    /**
     * Register new user
     *
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public static function register(array $data): ExecResult
    {
        $data['verification_code'] = rand(100000, 999999);
        $data['password'] = bcrypt($data['password'] ?? $data['verification_code']);

        DB::beginTransaction();
        try {
            /**
             * @var User $user
             * @var PersonalAccessTokenResult $token
             */
            $user = User::create(array_merge($data, [
                'timezone_uuid' => TimezoneRepository::getFirstByName('Europe/Ulyanovsk')->uuid // TODO: Remove
            ]));
            $user->assignRole($data['is_trainer'] ? User::ROLE_TRAINER : User::ROLE_USER);
            $token = $user->createToken('MyApp');

            DB::commit();

            return ExecResult::instance()
                ->setSuccess()
                ->setData(array_merge([
                    'access_token' => $token->accessToken,
                    'roles' => $user->getRoleNames(),
                    'verification_code' => (app()->environment() === 'production')
                        ? null
                        : $data['verification_code'],
                ], $user->toArray()));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset user password
     *
     * @param User $user
     * @return ExecResult
     */
    public static function resetPassword(User $user): ExecResult
    {
        try {
            /** @var PasswordReset $passwordReset */
            $passwordReset = PasswordResetRepository::getActualByUser($user);

            if (!$passwordReset) {
                $passwordReset = PasswordReset::create([
                    'user_uuid' => $user->uuid,
                    'reset_code' => rand(100000, 999999),
                    'expired_at' => Carbon::now()->addHours(3)
                ]);
            }
            SendSms::dispatch(
                $user->phone,
                __('sms.user.reset', ['code' => $passwordReset->reset_code])
            )->onConnection('redis');

            return ExecResult::instance()
                ->setSuccess()
                ->setData(
                    app()->environment() === 'production'
                        ? []
                        : ['passwordReset' => $passwordReset]
                );
        } catch (\Exception $e) {
            return ExecResult::instance()->setMessage($e->getMessage());
        }
    }
}

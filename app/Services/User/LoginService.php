<?php

namespace App\Services\User;

use App\Exceptions\Http\UnauthorizedHttpException;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\ExecResult;
use App\Repositories\PasswordResetRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Class LoginUserService
 * @package App\Services\User
 */
class LoginService
{
    /**
     * Login user
     *
     * @param array $data
     * @return ExecResult
     */
    public function login(array $data): ExecResult
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
}

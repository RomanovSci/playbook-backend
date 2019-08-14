<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Exceptions\Http\UnauthorizedHttpException;
use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class UserService
 * @package App\Services\User
 */
class UserService
{
    /**
     * @var SmsDeliveryService
     */
    protected $smsDeliveryService;

    /**
     * @var PasswordResetRepository
     */
    protected $passwordResetRepository;

    /**
     * UserService constructor.
     *
     * @param SmsDeliveryService $smsDeliveryService
     * @param PasswordResetRepository $passwordResetRepository
     */
    public function __construct(
        SmsDeliveryService $smsDeliveryService,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->smsDeliveryService = $smsDeliveryService;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Register new user
     *
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function register(array $data): ExecResult
    {
        $data['verification_code'] = Str::random(6);
        $data['password'] = bcrypt($data['password'] ?? $data['verification_code']);
        $data['status'] = $data['is_trainer'] ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;

        DB::beginTransaction();
        try {
            /**
             * @var User $user
             * @var PersonalAccessTokenResult $token
             */
            $user = User::create($data);
            $user->assignRole($data['is_trainer'] ? User::ROLE_TRAINER : User::ROLE_USER);
            $token = $user->createToken('MyApp');

            if (!isset($data['c_password'])) {
                $this->smsDeliveryService->send((string) $user->phone, $data['verification_code']);
            }

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
        $passwordReset = $this->passwordResetRepository->getActualByUser($user);

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
     * Reset user password
     *
     * @param User $user
     * @return ExecResult
     */
    public function resetPassword(User $user): ExecResult
    {
        try {
            /** @var PasswordReset $passwordReset */
            $passwordReset = $this->passwordResetRepository->getActualByUser($user);

            if (!$passwordReset) {
                $passwordReset = PasswordReset::create([
                    'user_uuid' => $user->uuid,
                    'reset_code' => Str::random(6),
                    'expired_at' => Carbon::now()->addHours(3),
                ]);
            }
            $this->smsDeliveryService->send($user->phone, __('sms.user.reset', [
                'code' => $passwordReset->reset_code
            ]));

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

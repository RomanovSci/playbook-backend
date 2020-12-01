<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\User;
use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class RegisterService
 * @package App\Services\User
 */
class RegisterService
{
    /**
     * @var SmsDeliveryService
     */
    protected $smsDeliveryService;

    /**
     * @param SmsDeliveryService $smsDeliveryService
     */
    public function __construct(SmsDeliveryService $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
    }

    /**
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function register(array $data): ExecResult
    {
        $data['verification_code'] = Str::random(6);
        $data['password'] = bcrypt($data['password'] ?? $data['verification_code']);
        $data['status'] = User::STATUS_ACTIVE;

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
}

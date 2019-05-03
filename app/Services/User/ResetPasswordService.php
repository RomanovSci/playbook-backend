<?php

namespace App\Services\User;

use App\Models\PasswordReset;
use App\Models\User;
use App\Services\ExecResult;
use App\Repositories\PasswordResetRepository;
use App\Services\SmsDelivery\SmsDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Class ResetPasswordService
 * @package App\Services\User
 */
class ResetPasswordService
{
    /**
     * @var SmsDeliveryService
     */
    protected $smsDeliveryService;

    /**
     * ResetPasswordService constructor.
     * @param SmsDeliveryService $smsDeliveryService
     */
    public function __construct(SmsDeliveryService $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
    }

    /**
     * Reset user password
     *
     * @param User $user
     * @return ExecResult
     */
    public function reset(User $user): ExecResult
    {
        try {
            /** @var PasswordReset $passwordReset */
            $passwordReset = PasswordResetRepository::getActualByUser($user);

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

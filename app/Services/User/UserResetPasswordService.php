<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\PasswordReset;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Class UserResetPasswordService
 * @package App\Services\User
 */
class UserResetPasswordService
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
     * @param User $user
     * @return ExecResult
     */
    public function reset(User $user): ExecResult
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
            return ExecResult::instance()->setSuccess(false)->setMessage($e->getMessage());
        }
    }
}

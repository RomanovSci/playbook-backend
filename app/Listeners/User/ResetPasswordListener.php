<?php

namespace App\Listeners\User;

use App\Events\User\ResetPasswordEvent;
use App\Services\SmsDeliveryService\SmsDeliveryServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class ResetPasswordListener
 * @package App\Listeners\User
 */
class ResetPasswordListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var SmsDeliveryServiceInterface
     */
    protected $smsDeliveryService;

    /**
     * ResetPasswordListener constructor.
     * @param SmsDeliveryServiceInterface $smsDeliveryService
     */
    public function __construct(SmsDeliveryServiceInterface $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
    }

    /**
     * Event handler
     *
     * @param ResetPasswordEvent $event
     * @return void
     */
    public function handle(ResetPasswordEvent $event)
    {
        if (app()->environment() === 'production') {
            $this->smsDeliveryService->send(
                $event->passwordReset->user->phone,
                $event->passwordReset->reset_code
            );
        }
    }
}

<?php

namespace App\Listeners\User;

use App\Events\User\RegisterUserEvent;
use App\Services\SmsDeliveryService\SmsDeliveryServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class RegisterUserListener
 * @package App\Listeners\User
 */
class RegisterUserListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var SmsDeliveryServiceInterface
     */
    protected $smsDeliveryService;

    /**
     * RegisterUserListener constructor.
     * @param SmsDeliveryServiceInterface $smsDeliveryService
     */
    public function __construct(SmsDeliveryServiceInterface $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
    }

    /**
     * Event handler
     *
     * @param RegisterUserEvent $event
     * @return void
     */
    public function handle(RegisterUserEvent $event)
    {
        if (app()->environment() === 'production') {
            $this->smsDeliveryService->send(
                $event->user->phone,
                $event->user->verification_code
            );
        }
    }
}

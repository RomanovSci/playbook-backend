<?php

namespace App\Listeners\User;

use App\Events\User\ResetPasswordEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class SendResetPasswordNotification
 * @package App\Listeners\User
 */
class SendResetPasswordNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Event handler
     *
     * @param ResetPasswordEvent $event
     * @return void
     */
    public function handle(ResetPasswordEvent $event)
    {
        //todo: Sending sms
    }
}

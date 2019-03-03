<?php

namespace App\Listeners\User;
use App\Events\User\ResetPasswordEvent;

/**
 * Class SendResetPasswordNotification
 * @package App\Listeners\User
 */
class SendResetPasswordNotification
{
    /**
     * Event handler
     *
     * @param ResetPasswordEvent $event
     * @return void
     */
    public function handle(ResetPasswordEvent $event)
    {
        //todo: Handler
    }
}

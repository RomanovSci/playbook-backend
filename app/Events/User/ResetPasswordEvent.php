<?php

namespace App\Events\User;

use App\Models\PasswordReset;
use Illuminate\Queue\SerializesModels;

/**
 * Class ResetPasswordEvent
 * @package App\Events\User
 */
class ResetPasswordEvent
{
    use SerializesModels;

    /**
     * @var PasswordReset
     */
    public $passwordReset;

    /**
     * ResetPasswordEvent constructor.
     * @param PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }
}

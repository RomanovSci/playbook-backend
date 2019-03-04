<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class RegisterNewUserEvent
 * @package App\Events\User
 */
class RegisterUserEvent
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * ResetPasswordEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}

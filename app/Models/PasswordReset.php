<?php

namespace App\Models;

/**
 * Class PasswordReset
 * @package App\Models
 *
 * @property string user_uuid
 * @property string reset_code
 */
class PasswordReset extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'password_resets';

    /**
     * @var array
     */
    protected $fillable = [
        'user_uuid',
        'reset_code',
        'expired_at',
    ];
}

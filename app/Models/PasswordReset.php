<?php

namespace App\Models;

/**
 * Class PasswordReset
 * @package App\Models
 *
 * @property string user_uuid
 * @property string reset_code
 *
 * @property string expired_at
 * @property string used_at
 *
 * @property User user
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


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

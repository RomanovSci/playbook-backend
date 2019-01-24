<?php

namespace App\Models;

/**
 * Class UserPlayground
 * @package App\Models
 *
 * @property integer id
 * @property integer user_id
 * @property integer playground_id
 */
class UserPlayground extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'playground_id'
    ];

    /**
     * @var string
     */
    protected $table = 'users_playgrounds';
}

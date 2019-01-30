<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPlayground
 * @package App\Models
 *
 * @property integer id
 * @property integer user_id
 * @property integer playground_id
 */
class UserPlayground extends Model
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

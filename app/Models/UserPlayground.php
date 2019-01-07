<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

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

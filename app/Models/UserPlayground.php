<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Class UserPlayground
 * @package App\Models
 *
 * @property string uuid
 * @property string user_uuid
 * @property string playground_uuid
 */
class UserPlayground extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'user_uuid',
        'playground_uuid',
    ];

    /**
     * @var string
     */
    protected $table = 'users_playgrounds';

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }
}

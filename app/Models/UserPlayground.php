<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPlayground
 * @package App\Models
 *
 * @property string user_uuid
 * @property string playground_uuid
 */
class UserPlayground extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_playground';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'user_uuid',
        'playground_uuid',
    ];
}

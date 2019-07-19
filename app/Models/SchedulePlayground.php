<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SchedulePlayground
 * @package App\Models
 *
 * @property string user_uuid
 * @property string playground_uuid
 */
class SchedulePlayground extends Model
{
    /**
     * @var string
     */
    protected $table = 'schedule_playground';

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
        'schedule_uuid',
        'playground_uuid'
    ];
}

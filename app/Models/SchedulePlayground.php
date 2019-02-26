<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SchedulePlayground
 * @package App\Models
 *
 * @property string user_uuid
 * @property string playground_uuid
 */
class SchedulePlayground extends BaseModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_uuid',
        'playground_uuid'
    ];

    /**
     * @var string
     */
    protected $table = 'schedules_playgrounds';
}

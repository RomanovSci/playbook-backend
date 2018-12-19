<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SchedulePlayground
 * @package App\Models
 *
 * @property integer id
 * @property integer user_id
 * @property integer playground_id
 */
class SchedulePlayground extends BaseModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_id', 'playground_id'
    ];

    /**
     * @var string
     */
    protected $table = 'schedules_playgrounds';
}

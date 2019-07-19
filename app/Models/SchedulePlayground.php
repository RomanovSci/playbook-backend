<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class SchedulePlayground
 * @package App\Models
 *
 * @property string user_uuid
 * @property string playground_uuid
 */
class SchedulePlayground extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'schedule_playground';

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_uuid',
        'playground_uuid'
    ];
}

<?php
declare(strict_types = 1);

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
     * @var string
     */
    protected $table = 'schedules_playgrounds';

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_uuid',
        'playground_uuid'
    ];
}

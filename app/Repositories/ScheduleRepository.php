<?php

namespace  App\Repositories;

use App\Models\Schedule;

/**
 * Class ScheduleRepository
 *
 * @package App\Repositories
 */
class ScheduleRepository
{
    /**
     * Get active schedules by schedulable type
     *
     * @param string $type
     * @return mixed
     */
    public static function getActiveByType(string $type)
    {
        return Schedule::where('schedulable_type', $type)->get();
    }
}

<?php

namespace  App\Repositories;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return mixed
     */
    public static function getActiveByTypeInRange(
        string $type,
        Carbon $startTime,
        Carbon $endTime
    ): Collection {
        return Schedule::where('schedulable_type', $type)
            ->where('start_time', '>=', $startTime->toDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->get();
    }
}

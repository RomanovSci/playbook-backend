<?php

namespace  App\Repositories;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ScheduleRepository
 * @package App\Repositories
 */
class ScheduleRepository
{
    /**
     * Get schedule by schedulable data
     *
     * @param string|null $schedulableType
     * @param int|null $schedulableId
     * @return Collection
     */
    public static function getBySchedulable(
        string $schedulableType = null,
        int $schedulableId = null
    ): Collection {
        return Schedule::where('schedulable_id', $schedulableId)
            ->where('schedulable_type', $schedulableType)
            ->get();
    }

    /**
     * Get active schedules by schedulable type
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $schedulableType
     * @param int $schedulableId
     * @return mixed
     */
    public static function getActiveInRange(
        Carbon $startTime,
        Carbon $endTime,
        string $schedulableType = null,
        int $schedulableId = null
    ): Collection {
        $query = Schedule::where('start_time', '>=', $startTime->toDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString());

        if ($schedulableType) {
            $query->where('schedulable_type', $schedulableType);
        }

        if ($schedulableId) {
            $query->where('schedulable_id', $schedulableId);
        }

        return $query->get();
    }
}

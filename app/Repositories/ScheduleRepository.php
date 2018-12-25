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
            ->orderBy('start_time', 'asc')
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
    public static function getByDateRange(
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

    /**
     * Get merged schedules for schedulable.
     * For example if we have 4 schedules with the next intervals:
     *
     * Collection = [
     *  2000-01-01 00:00:00 |-----| 2000-01-01 05:00:00,
     *        2000-01-01 05:00:00 |---| 2000-01-01 08:00:00,
     *            2000-01-01 08:00:00 |----------| 2000-01-01 18:00:00,
     *                          2000-01-01 19:00:00 |----------| 2000-01-01 23:00:00,
     * ]
     *
     * We can merge all with equals start & end points. Merge result:
     *
     * Collection = [
     *  2000-01-01 00:00:00 |--------------------| 2000-01-01 18:00:00,
     *                          2000-01-01 19:00:00 |----------| 2000-01-01 23:00:00
     * ]
     *
     * @param string $schedulableType
     * @param int $schedulableId
     * @return Collection
     */
    public static function getMergedSchedules(string $schedulableType, int $schedulableId): Collection
    {
        $schedules = self::getBySchedulable($schedulableType, $schedulableId);
        $mergedSchedules = new Collection();

        foreach ($schedules as $index => $schedule) {
            if (!$mergedSchedules->count()) {
                $mergedSchedule = new Schedule();
                $mergedSchedule->start_time = $schedule->start_time;
                $mergedSchedule->end_time = $schedule->end_time;

                $mergedSchedules->push($mergedSchedule);
                continue;
            }

            $lastMerged = $mergedSchedules->last();

            if ($schedule->start_time === $lastMerged->end_time) {
                $lastMerged->end_time = $schedule->end_time;
                continue;
            }

            $mergedSchedules->push(clone $schedule);
        }

        return $mergedSchedules;
    }
}

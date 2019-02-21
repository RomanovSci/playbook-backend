<?php

namespace  App\Repositories;

use App\Models\Schedule\Schedule;
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
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     */
    public static function getBySchedulable(
        string $schedulableType = null,
        int $schedulableId = null,
        Carbon $startTime = null,
        Carbon $endTime = null
    ): Collection {
        $query = Schedule::where('schedulable_id', $schedulableId)
            ->where('schedulable_type', $schedulableType)
            ->orderBy('start_time', 'asc');

        if ($startTime && $endTime) {
            $query->whereRaw("tsrange(schedules.start_time, schedules.end_time, '()') && tsrange(?, ?, '()')", [
                $startTime,
                $endTime
            ]);
        }

        return $query->get();
    }

    /**
     * Get active schedules by schedulable type
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int $limit
     * @param int $offset
     * @param string $schedulableType
     * @param int $schedulableId
     * @return mixed
     */
    public static function getByDateRange(
        Carbon $startTime,
        Carbon $endTime,
        int $limit = 100,
        int $offset = 0,
        string $schedulableType = null,
        int $schedulableId = null
    ): Collection {
        $query = Schedule::where('start_time', '>=', $startTime->toDateTimeString())
            ->orderBy('start_time', 'asc')
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->limit($limit)
            ->offset($offset);

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
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     */
    public static function getMergedSchedules(
        string $schedulableType,
        int $schedulableId,
        Carbon $startTime = null,
        Carbon $endTime = null
    ): Collection {
        $schedules = self::getBySchedulable(
            $schedulableType,
            $schedulableId,
            $startTime,
            $endTime
        );
        $mergedSchedules = new Collection();

        foreach ($schedules as $schedule) {
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

<?php

namespace  App\Repositories;

use App\Models\Schedule\MergedSchedule;
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
     * @param string|null $schedulableUuid
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     */
    public static function getBySchedulable(
        string $schedulableType = null,
        string $schedulableUuid = null,
        Carbon $startTime = null,
        Carbon $endTime = null
    ): Collection {
        $query = Schedule::where('schedulable_uuid', $schedulableUuid)
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
     * Get all schedules between dates
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int $limit
     * @param int $offset
     * @param string $schedulableType
     * @param string $schedulableUuid
     * @return mixed
     */
    public static function getBetween(
        Carbon $startTime,
        Carbon $endTime,
        int $limit = 100,
        int $offset = 0,
        string $schedulableType = null,
        string $schedulableUuid = null
    ): Collection {
        $query = Schedule::where('start_time', '>=', $startTime->toDateTimeString())
            ->orderBy('start_time', 'asc')
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->limit($limit)
            ->offset($offset);

        if ($schedulableType) {
            $query->where('schedulable_type', $schedulableType);
        }

        if ($schedulableUuid) {
            $query->where('schedulable_uuid', $schedulableUuid);
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
     * @param string $schedulableUuid
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     */
    public static function getMergedSchedules(
        string $schedulableType,
        string $schedulableUuid,
        Carbon $startTime = null,
        Carbon $endTime = null
    ): Collection {
        $schedules = ScheduleRepository::getBySchedulable(
            $schedulableType,
            $schedulableUuid,
            $startTime,
            $endTime
        );
        $mergedSchedules = new Collection();

        /** @var Schedule $schedule */
        foreach ($schedules as $schedule) {
            $mergedSchedule = new MergedSchedule();
            $mergedSchedule->start_time = $schedule->start_time;
            $mergedSchedule->end_time = $schedule->end_time;
            $mergedSchedule->price_per_hour = null;
            $mergedSchedule->currency = $schedule->currency;
            $mergedSchedule->setSchedule(clone $schedule);

            if (!$mergedSchedules->count()) {
                $mergedSchedules->push($mergedSchedule);
                continue;
            }

            /** @var MergedSchedule $lastMerged */
            $lastMerged = $mergedSchedules->last();

            if ($schedule->start_time->eq($lastMerged->end_time)) {
                $lastMerged->end_time = $schedule->end_time;
                $lastMerged->setSchedule(clone $schedule);
                continue;
            }

            $mergedSchedules->push($mergedSchedule);
        }

        return $mergedSchedules;
    }
}

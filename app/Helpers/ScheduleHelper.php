<?php

namespace App\Helpers;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Schedule\Schedule;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ScheduleHelper
 * @package App\Helpers
 */
class ScheduleHelper
{
    /**
     * Return schedule price per one minute
     *
     * @param Schedule $schedule
     * @return int
     */
    public static function getMinutesRate(Schedule $schedule): int
    {
        return money($schedule->price_per_hour, $schedule->currency)
            ->divide(60)
            ->getAmount();
    }

    /**
     * Check if schedulable has schedules
     * that overlaps with period (startTime - endTime).
     * Exclude $excludedSchedules from the checking
     *
     * @param Model $schedulable
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param array $excludedSchedules
     * @return bool
     *
     * @throws IncorrectDateRange
     */
    public static function periodsIsOverlaps(
        Model $schedulable,
        Carbon $startTime,
        Carbon $endTime,
        $excludedSchedules = []
    ): bool {
        $existedSchedules = ScheduleRepository::getBySchedulable(
            get_class($schedulable),
            $schedulable->uuid
        );

        /**
         * Exclude schedules that contains
         * in $excludedSchedules array
         */
        $existedSchedules = $existedSchedules->filter(
            function ($existedSchedule) use ($excludedSchedules) {
                foreach ($excludedSchedules as $excludedSchedule) {
                    if ($excludedSchedule->uuid === $existedSchedule->uuid) {
                        return false;
                    }
                }

                return true;
            }
        );

        /** Check schedules overlaps */
        foreach ($existedSchedules as $existedSchedule) {
            $scheduleStartTime = Carbon::parse($existedSchedule->start_time);
            $scheduleEndTime = Carbon::parse($existedSchedule->end_time);

            if (DateTimeHelper::timePeriodsIsOverlaps($startTime, $endTime, $scheduleStartTime, $scheduleEndTime)) {
                return true;
            }
        }

        return false;
    }
}

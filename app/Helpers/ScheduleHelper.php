<?php

namespace App\Helpers;

use App\Models\Schedule\Schedule;

/**
 * Class ScheduleHelper
 * @package App\Helpers
 */
class ScheduleHelper
{
    /**
     * Get schedule minutes rate
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
}

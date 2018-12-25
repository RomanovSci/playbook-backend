<?php

namespace App\Helpers;

use App\Exceptions\Internal\IncorrectDateRange;
use Carbon\Carbon;

class DateTimeHelper
{
    /**
     * Determinate if time periods is overlaps
     *
     * @param Carbon $firstStart
     * @param Carbon $firstEnd
     * @param Carbon $secondStart
     * @param Carbon $secondEnd
     * @return boolean
     *
     * @throws IncorrectDateRange
     */
    public static function timePeriodsIsOverlaps(
        Carbon $firstStart,
        Carbon $firstEnd,
        Carbon $secondStart,
        Carbon $secondEnd
    ): bool {
        if ($firstStart->greaterThanOrEqualTo($firstEnd) || $secondStart->greaterThanOrEqualTo($secondEnd)) {
            throw new IncorrectDateRange('Range is negative');
        }

        return !($firstStart->greaterThanOrEqualTo($secondEnd) || $secondStart->greaterThanOrEqualTo($firstEnd));
    }
}

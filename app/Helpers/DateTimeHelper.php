<?php
declare(strict_types = 1);

namespace App\Helpers;

use App\Exceptions\Internal\IncorrectDateRange;
use Carbon\Carbon;

/**
 * Class DateTimeHelper
 * @package App\Helpers
 */
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

    /**
     * Get overlapped minutes amount
     *
     * @param Carbon $firstStart
     * @param Carbon $firstEnd
     * @param Carbon $secondStart
     * @param Carbon $secondEnd
     * @return int
     *
     * @throws IncorrectDateRange
     */
    public static function getOverlappedMinutesAmount(
        Carbon $firstStart,
        Carbon $firstEnd,
        Carbon $secondStart,
        Carbon $secondEnd
    ): int {
        if (!DateTimeHelper::timePeriodsIsOverlaps($firstStart, $firstEnd, $secondStart, $secondEnd)) {
            return 0;
        }

        $resultStartTime = $firstStart->greaterThanOrEqualTo($secondStart) ? $firstStart : $secondStart;
        $resultEndTime = $firstEnd->greaterThanOrEqualTo($secondEnd) ? $secondEnd : $firstEnd;

        return $resultEndTime->diffInMinutes($resultStartTime);
    }
}

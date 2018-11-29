<?php

namespace App\Services\Booking;

use App\Models\Schedule;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

/**
 * Class BookingAvailabilityChecker
 *
 * @package App\Services\Booking
 */
class BookingAvailabilityChecker
{
    /**
     * Check booking availability
     *
     * @param Schedule $schedule
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return bool
     */
    public function isAvailable(Schedule $schedule, Carbon $startTime, Carbon $endTime): bool
    {
        $scheduleStartTime = Carbon::parse($schedule->start_time);
        $scheduleEndTime = Carbon::parse($schedule->end_time);

        if (
            !$startTime->between($scheduleStartTime, $scheduleEndTime) ||
            !$endTime->between($scheduleStartTime, $scheduleEndTime)
        ) {
            return false;
        }

        foreach (BookingRepository::getActiveBySchedule($schedule) as $booking) {
            $bookingStartTime = Carbon::parse($booking->start_time);
            $bookingEndTime = Carbon::parse($booking->end_time);

            if (
                $startTime->between($bookingStartTime, $bookingEndTime) ||
                $endTime->between($bookingStartTime, $bookingEndTime)
            ) {
                return false;
            }
        }

        return true;
    }
}

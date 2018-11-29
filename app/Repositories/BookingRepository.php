<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Schedule;

/**
 * Class BookingRepository
 *
 * @package App\Repositories
 */
class BookingRepository
{
    public static function getActiveBySchedule(Schedule $schedule)
    {
        return Booking::where('status', Booking::STATUS_ACTIVE)
            ->where('schedule_id', $schedule->id)
            ->get();
    }
}

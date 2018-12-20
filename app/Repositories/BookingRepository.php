<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookingRepository
 * @package App\Repositories
 */
class BookingRepository
{
    public static function getActiveBySchedule(Schedule $schedule): Collection
    {
        return Booking::where('status', Booking::STATUS_ACTIVE)
            ->where('schedule_id', $schedule->id)
            ->get();
    }
}

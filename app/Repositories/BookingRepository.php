<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookingRepository
 * @package App\Repositories
 */
class BookingRepository
{
    /**
     * Get bookings by bookable data
     *
     * @param string $bookableType
     * @param int $bookableId
     * @return Collection
     */
    public static function getByBookable(string $bookableType, int $bookableId): Collection
    {
        return Booking::where('bookable_type', $bookableType)
            ->with('playground')
            ->with('creator')
            ->where('bookable_id', $bookableId)
            ->get();
    }

    /**
     * Get bookings in time range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string|null $bookableType
     * @param int|null $bookableId
     * @param int|null $status
     * @return Collection
     */
    public static function getByDateRange(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType = null,
        int $bookableId = null,
        int $status = null
    ): Collection {
        $query = Booking::where('start_time', '>=', $startTime->toDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString());

        if ($bookableType) {
            $query->where('bookable_type', $bookableType);
        }

        if ($bookableId) {
            $query->where('bookable_id', $bookableId);
        }

        if (isset($status)) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Get confirmed bookings for schedule
     *
     * @param Schedule $schedule
     * @return Collection
     */
    public static function getConfirmedBookingsForSchedule(Schedule $schedule): Collection
    {
        return Booking::where('bookable_id', $schedule->schedulable_id)
            ->with('creator')
            ->where('bookable_type', $schedule->schedulable_type)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereRaw("tsrange(bookings.start_time, bookings.end_time, '()') && tsrange(?, ?, '()')", [
                $schedule->start_time,
                $schedule->end_time
            ])
            ->orderBy('start_time', 'asc')
            ->get();
    }
}

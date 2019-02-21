<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Schedule\Schedule;
use App\Models\User;
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
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int $limit
     * @param int $offset
     * @param string $bookableType
     * @param int $bookableId
     * @return Collection
     */
    public static function getByBookable(
        Carbon $startTime,
        Carbon $endTime,
        int $limit,
        int $offset,
        string $bookableType,
        int $bookableId
    ): Collection {
        return Booking::where('bookable_type', $bookableType)
            ->where('bookable_id', $bookableId)
            ->where('start_time', '>=', $startTime->toDayDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->with('playground')
            ->with('creator')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get bookings by creator
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int $limit
     * @param int $offset
     * @param User $user
     * @return Collection
     */
    public static function getByCreator(
        Carbon $startTime,
        Carbon $endTime,
        int $limit,
        int $offset,
        User $user
    ): Collection {
        return Booking::where('creator_id', $user->id)
            ->where('start_time', '>=', $startTime->toDayDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->with('playground')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Get bookings between dates
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string|null $bookableType
     * @param int|null $bookableId
     * @param int|null $status
     * @return Collection
     */
    public static function getBetween(
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
    public static function getConfirmedForSchedule(Schedule $schedule): Collection
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

    /**
     * Get confirmed bookings in date range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     */
    public static function getConfirmedInDatesRange(Carbon $startTime, Carbon $endTime): Collection
    {
        return Booking::where('status', Booking::STATUS_CONFIRMED)
            ->whereRaw("tsrange(bookings.start_time, bookings.end_time, '()') && tsrange(?, ?, '()')", [
                $startTime,
                $endTime
            ])
            ->orderBy('start_time', 'asc')
            ->get();
    }
}

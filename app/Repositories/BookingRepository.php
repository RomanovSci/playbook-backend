<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\Queries\TimeIntervalQueries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookingRepository
 * @package App\Repositories
 */
class BookingRepository extends Repository
{
    use TimeIntervalQueries;

    protected const MODEL = Booking::class;

    /**
     * Get bookings by bookable data
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param int $limit
     * @param int $offset
     * @param string $bookableType
     * @param string $bookableUuid
     * @return Collection
     */
    public function getByBookable(
        Carbon $startTime,
        Carbon $endTime,
        int $limit,
        int $offset,
        string $bookableType,
        string $bookableUuid
    ): Collection {
        $bookings = $this->builder()
            ->where('bookable_type', $bookableType)
            ->where('bookable_uuid', $bookableUuid)
            ->where('start_time', '>=', $startTime->toDayDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->with(['equipmentsRent.equipment'])
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $bookings;
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
    public function getByCreator(
        Carbon $startTime,
        Carbon $endTime,
        int $limit,
        int $offset,
        User $user
    ): Collection {
        return $this->builder()
            ->where('creator_uuid', $user->uuid)
            ->where('start_time', '>=', $startTime->toDayDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString())
            ->with(['equipmentsRent.equipment'])
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
     * @param string|null $bookableUuid
     * @param int|null $status
     * @return Collection
     */
    public function getBetween(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType = null,
        string $bookableUuid = null,
        int $status = null
    ): Collection {
        $query = $this->builder()
            ->where('start_time', '>=', $startTime->toDateTimeString())
            ->where('end_time', '<=', $endTime->toDayDateTimeString());

        if ($bookableType) {
            $query->where('bookable_type', $bookableType);
        }

        if ($bookableUuid) {
            $query->where('bookable_uuid', $bookableUuid);
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
     * @throws IncorrectDateRange
     */
    public function getConfirmedForSchedule(Schedule $schedule): Collection
    {
        return $this->intersectsWith($schedule->start_time, $schedule->end_time)
            ->where('bookable_uuid', $schedule->schedulable_uuid)
            ->where('bookable_type', $schedule->schedulable_type)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get confirmed bookings in date range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return Collection
     * @throws IncorrectDateRange
     */
    public function getConfirmedInDatesRange(Carbon $startTime, Carbon $endTime): Collection
    {
        return $this->intersectsWith($startTime, $endTime)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->orderBy('start_time', 'asc')
            ->get();
    }
}

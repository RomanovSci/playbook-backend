<?php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookingRepository
 * @package App\Repositories
 */
class BookingRepository
{
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
}

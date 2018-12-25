<?php

namespace App\Services;

use App\Helpers\DateTimeHelper;
use App\Models\Booking;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;

/**
 * Class BookingAvailabilityChecker
 * @package App\Services\Booking
 */
class BookingService
{
    /**
     * Determinate if booking can be create
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param User $creator
     * @param string $bookableType
     * @param int $bookableId
     * @return array
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public function checkBookingRequest(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        int $bookableId,
        User $creator
    ): array {
        $result = [
            'success' => false,
            'message' => '',
        ];

        /** Can't book unbookable entities */
        if (!in_array($bookableType, [User::class, Playground::class])) {
            $result['message'] = 'Incorrect bookable type';
            return $result;
        }

        /** Can't create booking for myself */
        if ($bookableType === User::class && $creator->id === $bookableId) {
            $result['message'] = 'Can\'t create booking for myself';
            return $result;
        }

        /** Find the proper schedule for booking dates */
        $properSchedule = null;
        $mergedSchedules = ScheduleRepository::getMergedSchedules($bookableType, $bookableId);

        foreach ($mergedSchedules as $mergedSchedule) {
            if (Carbon::parse($mergedSchedule->start_time)->lessThanOrEqualTo($startTime) &&
                Carbon::parse($mergedSchedule->end_time)->greaterThanOrEqualTo($endTime)) {
                $properSchedule = $mergedSchedule;
            }
        }

        /** Can't create booking for not existed schedules */
        if (!$properSchedule) {
            $result['message'] = 'Schedules for this time interval doesn\'t exists';
            return $result;
        }

        /**
         * Check if exists confirmed
         * booking for the proper schedule
         */
        $confirmedBookings = BookingRepository::getByDateRange(
            Carbon::parse($properSchedule->start_time),
            Carbon::parse($properSchedule->end_time),
            $bookableType,
            $bookableId,
            Booking::STATUS_CONFIRMED
        );

        foreach ($confirmedBookings as $confirmedBooking) {
            if (DateTimeHelper::timePeriodsIsOverlaps(
                Carbon::parse($confirmedBooking->start_time),
                Carbon::parse($confirmedBooking->end_time),
                $startTime,
                $endTime
            )) {
                /** Can't book reserved period */
                $result['message'] = 'This time already reserved';
                return $result;
            }
        }

        $result['success'] = true;
        return $result;
    }
}

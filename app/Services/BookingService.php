<?php

namespace App\Services;

use App\Helpers\DateTimeHelper;
use App\Helpers\ScheduleHelper;
use App\Models\Booking;
use App\Models\Playground;
use App\Models\Schedule\MergedSchedule;
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
     * Determinate if booking can be create and get booking price
     * TODO: Refactoring
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param int $bookableId
     * @return array
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public function getBookingPrice(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        int $bookableId
    ): array {
        $result = ['success' => false, 'message' => '', 'data' => []];

        /** Can't get price for unbookable entities */
        if (!in_array($bookableType, [User::class, Playground::class])) {
            $result['message'] = __('errors.incorrect_bookable_type');
            return $result;
        }

        /**
         * Find the proper schedule for booking dates
         *
         * @var MergedSchedule $properSchedule
         */
        $properSchedule = null;
        $mergedSchedules = ScheduleRepository::getMergedSchedules(
            $bookableType,
            $bookableId,
            $startTime,
            $endTime
        );

        foreach ($mergedSchedules as $mergedSchedule) {
            if (Carbon::parse($mergedSchedule->start_time)->lessThanOrEqualTo($startTime) &&
                Carbon::parse($mergedSchedule->end_time)->greaterThanOrEqualTo($endTime)) {
                $properSchedule = $mergedSchedule;
            }
        }

        /** Can't get booking price for not existed schedules */
        if (!$properSchedule) {
            $result['message'] = __('errors.schedule_time_unavailable');
            return $result;
        }

        /**
         * Check if exists confirmed
         * booking for the proper schedule
         */
        $confirmedBookings = BookingRepository::getBetween(
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
                /** Can't get price for reserved period */
                $result['message'] = __('errors.time_already_reserved');
                return $result;
            }
        }

        $price = 0;
        $currencySubunit = currency($properSchedule->currency)->getSubunit();

        foreach ($properSchedule->getSchedules() as $schedule) {
            $minutesRate = ScheduleHelper::getMinutesRate($schedule);
            $overlappedMinutes = DateTimeHelper::getOverlappedMinutesAmount(
                $startTime,
                $endTime,
                Carbon::parse($schedule->start_time),
                Carbon::parse($schedule->end_time)
            );
            $price += round(
                (
                    money($minutesRate, $properSchedule->currency)
                        ->multiply($overlappedMinutes)
                        ->getAmount()) / $currencySubunit
                ) * $currencySubunit;
        }

        $result['success'] = true;
        $result['data'] = [
            'currency' => $properSchedule->currency,
            'price' => $price,
        ];

        return $result;
    }

    /**
     * Determinate if booking can be confirmed
     *
     * @param Booking $booking
     * @return array
     */
    public function canConfirm(Booking $booking): array
    {
        $result = ['success' => false, 'message' => '',];
        $confirmedBookingsCount = BookingRepository::getConfirmedInDatesRange(
            Carbon::parse($booking->start_time),
            Carbon::parse($booking->end_time)
        )->count();

        if ($confirmedBookingsCount === 0) {
            $result['success'] = true;
        } else {
            $result['message'] = __('errors.booking_time_busy');
        }

        return $result;
    }
}

<?php

namespace App\Services;

use App\Helpers\DateTimeHelper;
use App\Helpers\ScheduleHelper;
use App\Models\Booking;
use App\Models\Playground;
use App\Models\Schedule\MergedSchedule;
use App\Models\Schedule\Schedule;
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
     * Get booking price
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return array
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public function getBookingPrice(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): array {
        $findScheduleResult = BookingService::findAppropriateSchedule(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid
        );

        if (!$findScheduleResult['success']) {
            return $findScheduleResult;
        }

        $appropriateSchedule = $findScheduleResult['schedule'];
        $scheduleAvailabilityCheckResult = BookingService::checkScheduleTimeAvailability(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid,
            $appropriateSchedule
        );

        if (!$scheduleAvailabilityCheckResult['success']) {
            return $scheduleAvailabilityCheckResult;
        }

        $price = 0;
        $currencySubunit = currency($appropriateSchedule->currency)->getSubunit();

        foreach ($appropriateSchedule->getSchedules() as $schedule) {
            $minutesRate = ScheduleHelper::getMinutesRate($schedule);
            $overlappedMinutes = DateTimeHelper::getOverlappedMinutesAmount(
                $startTime,
                $endTime,
                Carbon::parse($schedule->start_time),
                Carbon::parse($schedule->end_time)
            );
            $price += round(
                (
                    money($minutesRate, $appropriateSchedule->currency)
                        ->multiply($overlappedMinutes)
                        ->getAmount()) / $currencySubunit
                ) * $currencySubunit;
        }

        $result['success'] = true;
        $result['data'] = [
            'currency' => $appropriateSchedule->currency,
            'price' => $price,
        ];

        return $result;
    }

    /**
     * Find appropriate schedule for dates range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return array
     */
    public static function findAppropriateSchedule(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): array {
        $result = [
            'success' => false,
            'message' => '',
        ];

        if (!in_array($bookableType, [User::class, Playground::class])) {
            $result['message'] = __('errors.incorrect_bookable_type');
            return $result;
        }

        /**
         * Find the appropriate schedule for required dates
         * @var MergedSchedule $appropriateSchedule
         */
        $appropriateSchedule = null;
        $mergedSchedules = ScheduleRepository::getMergedSchedules(
            $bookableType,
            $bookableUuid,
            $startTime,
            $endTime
        );

        foreach ($mergedSchedules as $mergedSchedule) {
            if (Carbon::parse($mergedSchedule->start_time)->lessThanOrEqualTo($startTime) &&
                Carbon::parse($mergedSchedule->end_time)->greaterThanOrEqualTo($endTime)) {
                $appropriateSchedule = $mergedSchedule;
            }
        }

        if (!$appropriateSchedule) {
            $result['message'] = __('errors.schedule_time_unavailable');
            return $result;
        }

        $result['success'] = true;
        $result['schedule'] = $appropriateSchedule;

        return $result;
    }

    /**
     * Check schedule time availability
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @param Schedule $schedule
     * @return array
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public static function checkScheduleTimeAvailability(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid,
        Schedule $schedule
    ): array {
        $result = ['success' => false, 'message' => ''];

        $confirmedBookings = BookingRepository::getBetween(
            Carbon::parse($schedule->start_time),
            Carbon::parse($schedule->end_time),
            $bookableType,
            $bookableUuid,
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

        $result['success'] = true;
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

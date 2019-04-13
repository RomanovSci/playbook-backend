<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\MergedSchedule;
use App\Services\ExecResult;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

/**
 * Class BookingHelper
 * @package App\Helpers
 */
class BookingHelper
{
    /**
     * Check if booking has available time
     * and can be confirmed by trainer
     *
     * @param Booking $booking
     * @return ExecResult
     */
    public static function timeIsAvailable(Booking $booking): ExecResult
    {
        $confirmedBookingsCount = BookingRepository::getConfirmedInDatesRange(
            Carbon::parse($booking->start_time),
            Carbon::parse($booking->end_time)
        )->count();

        if ($confirmedBookingsCount === 0) {
            return ExecResult::instance()->setSuccess();
        }

        return ExecResult::instance()->setMessage(__('errors.booking_time_busy'));
    }

    /**
     * Get booking price for period ($startTime - $endTime)
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return ExecResult
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public static function getBookingPrice(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): ExecResult {
        $getScheduleResult = ScheduleHelper::getAppropriateSchedule(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid
        );

        if (!$getScheduleResult->getSuccess()) {
            return $getScheduleResult;
        }

        /** @var MergedSchedule $appropriateSchedule */
        $appropriateSchedule = $getScheduleResult->getData('schedule');
        $scheduleAvailabilityCheckResult = ScheduleHelper::scheduleTimeIsAvailable(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid,
            $appropriateSchedule
        );

        if (!$scheduleAvailabilityCheckResult->getSuccess()) {
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
                (money($minutesRate, $appropriateSchedule->currency)
                    ->multiply($overlappedMinutes)
                    ->getAmount()) / $currencySubunit
            ) * $currencySubunit;
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData([
                'currency' => $appropriateSchedule->currency,
                'price' => $price,
            ]);
    }
}

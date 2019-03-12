<?php

namespace App\Services;

use App\Helpers\DateTimeHelper;
use App\Helpers\ScheduleHelper;
use App\Jobs\SendSms;
use App\Models\Booking;
use App\Models\Playground;
use App\Models\Schedule\MergedSchedule;
use App\Models\Schedule\Schedule;
use App\Models\User;
use App\Objects\Service\ExecResult;
use App\Repositories\BookingRepository;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $findScheduleResult = BookingService::findAppropriateSchedule(
            $startTime,
            $endTime,
            $bookableType,
            $bookableUuid
        );

        if (!$findScheduleResult->getSuccess()) {
            return $findScheduleResult;
        }

        /** @var MergedSchedule $appropriateSchedule */
        $appropriateSchedule = $findScheduleResult->getData('schedule');
        $scheduleAvailabilityCheckResult = BookingService::checkScheduleTimeAvailability(
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
                (
                    money($minutesRate, $appropriateSchedule->currency)
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

    /**
     * Find appropriate schedule for dates range
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @return ExecResult
     */
    public static function findAppropriateSchedule(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid
    ): ExecResult {
        if (!in_array($bookableType, [User::class, Playground::class])) {
            return ExecResult::instance()->setMessage(__('errors.incorrect_bookable_type'));
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
            return ExecResult::instance()->setMessage(__('errors.schedule_time_unavailable'));
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData([
                'schedule' => $appropriateSchedule
            ]);
    }

    /**
     * Check schedule time availability
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param string $bookableUuid
     * @param Schedule $schedule
     * @return ExecResult
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public static function checkScheduleTimeAvailability(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        string $bookableUuid,
        Schedule $schedule
    ): ExecResult {
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
                return ExecResult::instance()->setMessage(__('errors.time_already_reserved'));
            }
        }

        return ExecResult::instance()->setSuccess();
    }

    /**
     * Determinate if booking can be confirmed
     *
     * @param Booking $booking
     * @return ExecResult
     */
    public static function canConfirm(Booking $booking): ExecResult
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
     * Change booking status
     *
     * @param Booking $booking
     * @param int $status
     * @param string|null $note
     * @return ExecResult
     */
    public static function changeBookingStatus(
        Booking $booking,
        int $status,
        string $note = null
    ): ExecResult {
        if ($booking->status === $status) {
            return ExecResult::instance()->setMessage(__('errors.status_already_set'));
        }

        $booking->status = $status;
        $booking->note = $note;
        $result = $booking->update(['status', 'note']);

        if ($result) {
            /**
             * Send sms to user or trainer if
             * booking was successful declined
             */
            if ($status === Booking::STATUS_DECLINED && $booking->bookable_type === User::class) {
                $declineByUser = $booking->bookable_uuid !== Auth::user()->uuid;
                $phone = $declineByUser ? $booking->bookable->phone : $booking->creator->phone;
                $text = $declineByUser
                    ? __('sms.booking.decline_by_user')
                    : __('sms.booking.decline_by_trainer');

                SendSms::dispatch($phone, $text)->onConnection('redis');
            }

            /**
             * Send sms to user if booking
             * was successful confirmed
             */
            if (
                $status === Booking::STATUS_CONFIRMED &&
                $booking->bookable_type === User::class &&
                $booking->bookable_uuid !== Auth::user()->uuid
            ) {
                SendSms::dispatch(
                    $booking->creator->phone,
                    __('sms.booking.confirm')
                )->onConnection('redis');
            }
        }

        return ExecResult::instance()->setSuccess();
    }
}

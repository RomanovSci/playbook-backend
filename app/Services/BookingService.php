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

        return [
            'success' => true,
            'data' => [
                'currency' => $appropriateSchedule->currency,
                'price' => $price,
            ]
        ];
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

    /**
     * Change booking status
     *
     * @param Booking $booking
     * @param int $status
     * @param string|null $note
     * @return array
     */
    public function changeBookingStatus(
        Booking $booking,
        int $status,
        string $note = null
    ): array {
        if ($booking->status === $status) {
            return [
                'success' => false,
                'message' => __('errors.status_already_set'),
            ];
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

        return [
            'success' => $result,
            'message' => null,
        ];
    }
}

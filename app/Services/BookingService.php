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

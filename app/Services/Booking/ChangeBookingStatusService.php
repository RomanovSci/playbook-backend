<?php

namespace App\Services\Booking;

use App\Jobs\SendSms;
use App\Models\Booking;
use App\Models\User;
use App\Objects\Service\ExecResult;
use Illuminate\Support\Facades\Auth;

/**
 * Class ChangeBookingStatusService
 * @package App\Services\Booking
 */
class ChangeBookingStatusService
{
    /**
     * Change booking status
     *
     * @param Booking $booking
     * @param int $status
     * @param string|null $note
     * @return ExecResult
     */
    public function run(Booking $booking, int $status, string $note = null): ExecResult
    {
        if ($booking->status === $status) {
            return ExecResult::instance()->setMessage(__('errors.status_already_set'));
        }

        /** @var User $user */
        $user = Auth::user();
        $booking->status = $status;
        $booking->note = $note;
        $result = $booking->update(['status', 'note']);

        if ($result) {
            /**
             * Send sms to user or trainer if
             * booking was successful declined
             */
            if ($status === Booking::STATUS_DECLINED && $booking->bookable_type === User::class) {
                $declineByUser = $booking->bookable_uuid !== $user->uuid;
                $phone = $declineByUser ? $booking->bookable->phone : $booking->creator->phone;
                $text = $declineByUser
                    ? __('sms.booking.decline_by_user', [
                        'player_name' => $user->getFullName(),
                        'date' => $booking->start_time
                            ->addHours($booking->bookable->timezone->offset)
                            ->format('d-m-Y H:i'),
                        'note' => $booking->note,
                    ])
                    : __('sms.booking.decline_by_trainer', [
                        'trainer_name' => $booking->bookable->getFullName(),
                        'date' => $booking->start_time
                            ->addHours($user->timezone->offset)
                            ->format('d-m-Y'),
                        'note' => $booking->note,
                    ]);

                SendSms::dispatch($phone, $text)->onConnection('redis');
            }

            /**
             * Send sms to user if booking
             * was successful confirmed
             */
            if ($status === Booking::STATUS_CONFIRMED &&
                $booking->bookable_type === User::class &&
                $booking->bookable_uuid !== $booking->creator_uuid
            ) {
                SendSms::dispatch(
                    $booking->creator->phone,
                    __('sms.booking.confirm', [
                        'trainer_name' => $booking->bookable->getFullName(),
                        'date' => $booking->start_time
                            ->addHours($booking->creator->timezone->offset)
                            ->format('d-m-Y'),
                        'start_time' => $booking->start_time
                            ->addHours($booking->creator->timezone->offset)
                            ->format('H:i'),
                    ])
                )->onConnection('redis');
            }
        }

        return ExecResult::instance()->setSuccess();
    }
}

<?php

namespace App\Services;

use App\Helpers\BookingHelper;
use App\Jobs\SendSms;
use App\Models\Booking;
use App\Models\User;
use App\Objects\Service\ExecResult;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingAvailabilityChecker
 * @package App\Services\Booking
 */
class BookingService
{
    /**
     * Create booking
     *
     * @param User $creator
     * @param string $bookableType
     * @param array $data
     * @return ExecResult
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     */
    public static function create(
        User $creator,
        string $bookableType,
        array $data
    ): ExecResult {
        $bookableUuid = $data['bookable_uuid'];
        $getPriceResult = BookingHelper::getBookingPrice(
            Carbon::parse($data['start_time']),
            Carbon::parse($data['end_time']),
            $bookableType,
            $bookableUuid
        );

        if (!$getPriceResult->getSuccess()) {
            return $getPriceResult;
        }

        /** @var Booking $booking */
        $booking = Booking::create(array_merge($data, [
            'bookable_type' => $bookableType,
            'creator_uuid' => $creator->uuid,
            'price' => $getPriceResult->getData('price'),
            'currency' => $getPriceResult->getData('currency'),
        ]));

        if ($bookableType === User::class && $bookableUuid !== $creator->uuid) {
            $timezoneOffset = $creator->timezone->offset;
            SendSms::dispatch(
                UserRepository::getByUuid($bookableUuid)->phone,
                __('sms.booking.create', [
                    'player_name' => $creator->getFullName(),
                    'date' => $booking->start_time->addHours($timezoneOffset)->format('d-m-Y'),
                    'start_time' => $booking->start_time->addHours($timezoneOffset)->format('H:i'),
                    'end_time' => $booking->end_time->addHours($timezoneOffset)->format('H:i'),
                ])
            )->onConnection('redis');
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData($booking->toArray());
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

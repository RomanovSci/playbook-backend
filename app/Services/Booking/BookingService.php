<?php
declare(strict_types = 1);

namespace App\Services\Booking;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Booking;
use App\Models\EquipmentRent;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\ExecResult;
use App\Services\SmsDelivery\SmsDeliveryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingService
 * @package App\Services\Booking
 */
class BookingService
{
    /**
     * @var SmsDeliveryService
     */
    protected $smsDeliveryService;

    /**
     * @var BookingPricingService
     */
    protected $bookingPricingService;

    /**
     * BookingChangeStatusService constructor.
     * @param SmsDeliveryService $smsDeliveryService
     * @param BookingPricingService $bookingPricingService
     */
    public function __construct(
        SmsDeliveryService $smsDeliveryService,
        BookingPricingService $bookingPricingService
    ) {
        $this->smsDeliveryService = $smsDeliveryService;
        $this->bookingPricingService = $bookingPricingService;
    }

    /**
     * Create booking
     *
     * @param User $creator
     * @param string $bookableType
     * @param array $data
     * @return ExecResult
     * @throws IncorrectDateRange
     */
    public function create(User $creator, string $bookableType, array $data): ExecResult
    {
        $bookableUuid = $data['bookable_uuid'];
        $getPriceResult = $this->bookingPricingService->getBookingPrice(
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
        $equipmentsRent = [];

        if (isset($data['equipments'])) {
            foreach ($data['equipments'] as $equipment) {
                /** @var EquipmentRent $equipmentRent */
                $equipmentRent = EquipmentRent::create([
                    'booking_uuid' => $booking->uuid,
                    'equipment_uuid' => $equipment['uuid'],
                    'count' => $equipment['count'],
                ]);

                $equipmentsRent[] = [
                    'count' => $equipmentRent->count,
                    'equipment' => $equipmentRent->equipment,
                ];
            }
        }

        if ($bookableType === User::class && $bookableUuid !== $creator->uuid) {
            $timezoneOffset = $creator->timezone->offset;
            $this->smsDeliveryService->send(
                UserRepository::getByUuid($bookableUuid)->phone,
                __('sms.booking.create', [
                    'player_name' => $creator->getFullName(),
                    'date' => $booking->start_time->addHours($timezoneOffset)->format('d-m-Y'),
                    'start_time' => $booking->start_time->addHours($timezoneOffset)->format('H:i'),
                    'end_time' => $booking->end_time->addHours($timezoneOffset)->format('H:i'),
                ])
            );
        }

        return ExecResult::instance()
            ->setSuccess()
            ->setData(array_merge(
                $booking->refresh()->toArray(),
                ['equipments_rent' => $equipmentsRent]
            ));
    }

    /**
     * Change booking status
     *
     * @param Booking $booking
     * @param int $status
     * @param string|null $note
     * @return ExecResult
     */
    public function changeStatus(Booking $booking, int $status, string $note = null): ExecResult
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
                $phone = (string) ($declineByUser ? $booking->bookable->phone : $booking->creator->phone);
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

                $this->smsDeliveryService->send($phone, $text);
            }

            /**
             * Send sms to user if booking
             * was successful confirmed
             */
            if ($status === Booking::STATUS_CONFIRMED &&
                $booking->bookable_type === User::class &&
                $booking->bookable_uuid !== $booking->creator_uuid
            ) {
                $this->smsDeliveryService->send(
                    (string) $booking->creator->phone,
                    __('sms.booking.confirm', [
                        'trainer_name' => $booking->bookable->getFullName(),
                        'date' => $booking->start_time
                            ->addHours($booking->creator->timezone->offset)
                            ->format('d-m-Y'),
                        'start_time' => $booking->start_time
                            ->addHours($booking->creator->timezone->offset)
                            ->format('H:i'),
                    ])
                );
            }
        }

        return ExecResult::instance()->setSuccess();
    }
}

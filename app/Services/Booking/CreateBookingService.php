<?php

namespace App\Services\Booking;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\BookingHelper;
use App\Models\Booking;
use App\Models\EquipmentRent;
use App\Models\User;
use App\Services\ExecResult;
use App\Repositories\UserRepository;
use App\Services\SmsDelivery\SmsDeliveryService;
use Carbon\Carbon;

/**
 * Class CreateBookingService
 * @package App\Services\Booking
 */
class CreateBookingService
{
    /**
     * @var SmsDeliveryService
     */
    protected $smsDeliveryService;

    /**
     * ChangeBookingStatusService constructor.
     * @param SmsDeliveryService $smsDeliveryService
     */
    public function __construct(SmsDeliveryService $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
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
    public function run(User $creator, string $bookableType, array $data): ExecResult
    {
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
}

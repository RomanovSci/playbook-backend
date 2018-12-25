<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Playground;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class BookingAvailabilityChecker
 * @package App\Services\Booking
 */
class BookingService
{
    /**
     * Create booking
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param string $bookableType
     * @param int $bookableId
     * @param User $user
     * @return Booking
     */
    public function create(
        Carbon $startTime,
        Carbon $endTime,
        string $bookableType,
        int $bookableId,
        User $user
    ): Booking {
        return Booking::create([
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'bookable_type' => $bookableType,
            'bookable_id' => $bookableId,
            'creator_id' => $user->id,
        ]);
    }

    /**
     * Check booking create ability
     *
     * @param User $creator
     * @param string $bookableType
     * @param int $bookableId
     * @return array
     */
    public function canCreate(
        User $creator,
        string $bookableType,
        int $bookableId
    ): array {
        $result = [
            'success' => false,
            'message' => '',
        ];

        /** Can't book unbookable entities */
        if (!in_array($bookableType, [User::class, Playground::class])) {
            $result['message'] = 'Incorrect bookable type';
            return $result;
        }

        /** Can't create booking for myself */
        if ($bookableType === User::class && $creator->id === $bookableId) {
            $result['message'] = 'Incorrect bookable id';
            return $result;
        }

        $result['success'] = true;
        return $result;
    }
}

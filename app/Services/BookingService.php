<?php

namespace App\Services;

use App\Exceptions\Internal\IncorrectBookableType;
use App\Models\Booking;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingAvailabilityChecker
 *
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
     * @param string $bookableType
     * @param int $bookableId
     * @return bool
     *
     * @throws IncorrectBookableType
     */
    public function canCreate(
        string $bookableType,
        int $bookableId
    ): bool {
        /** @var User $user */
        $user = Auth::user();

        if (!in_array($bookableType, [User::class, Playground::class])) {
            throw new IncorrectBookableType();
        }

        /** Can't create booking for myself */
        if ($bookableType === User::class && $user->id === $bookableId) {
            return false;
        }

        return true;
    }
}

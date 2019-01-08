<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Playground;
use App\Models\User;

/**
 * Class BookingPolicy
 * @package App\Policies
 */
class BookingPolicy
{
    /**
     * Determine if user can get bookings
     *
     * @param User $user
     * @param string $bookableType
     * @param int $bookableId
     * @return bool
     */
    public function getBookingsList(User $user, string $bookableType, int $bookableId): bool
    {
        if ($bookableType === User::class) {
            return $user->id === $bookableId;
        }
        //TODO: Checking for playground owner

        return false;
    }

    /**
     * Determine if the booking can be confirmed by user
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function confirmBooking(User $user, Booking $booking): bool
    {
        /**
         * Trainer can confirm
         * self booking requests
         */
        if ($booking->bookable_type === User::class) {
            return $booking->bookable_id === $user->id;
        }

        /**
         * Playground admin can confirm
         * self booking requests
         */
        if ($booking->bookable_type === Playground::class) {
            return $booking->bookable->creator_id === $user->id;
        }

        return false;
    }
}

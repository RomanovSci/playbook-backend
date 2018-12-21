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

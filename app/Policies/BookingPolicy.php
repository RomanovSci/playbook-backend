<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

/**
 * Class BookingPolicy
 *
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
        //TODO: Policy
        return true;
    }
}

<?php
declare(strict_types = 1);

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
     * @param string $bookableUuid
     * @return bool
     */
    public function getBookingsList(User $user, string $bookableType, string $bookableUuid): bool
    {
        if ($bookableType === User::class) {
            return $user->uuid === $bookableUuid;
        }

        if ($bookableType === Playground::class) {
            /** @var Playground $playground */
            $playground = Playground::find($bookableUuid);

            if (!$playground || !$playground->organization_uuid) {
                return false;
            }

            return $playground->organization->owner_uuid === $user->uuid;
        }

        return false;
    }

    /**
     * Determine if the booking can be confirm by user
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function confirmBooking(User $user, Booking $booking): bool
    {
        return $this->manageBooking($user, $booking);
    }

    /**
     * Determine if the booking can be decline by user
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    public function declineBooking(User $user, Booking $booking): bool
    {
        return $this->manageBooking($user, $booking) || $booking->creator_uuid == $user->uuid;
    }

    /**
     * Determine if the booking can be manage by user
     *
     * @param User $user
     * @param Booking $booking
     * @return bool
     */
    protected function manageBooking(User $user, Booking $booking): bool
    {
        /**
         * Trainer can manage
         * self booking requests
         */
        if ($booking->bookable_type === User::class) {
            return $booking->bookable_uuid === $user->uuid;
        }

        /**
         * Playground admin can manage
         * self booking requests
         */
        if ($booking->bookable_type === Playground::class) {
            return $booking->bookable->creator_uuid === $user->uuid;
        }

        return false;
    }
}

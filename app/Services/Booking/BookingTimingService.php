<?php
declare(strict_types = 1);

namespace App\Services\Booking;

use App\Exceptions\Internal\IncorrectDateRange;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Services\ExecResult;
use Carbon\Carbon;

/**
 * Class BookingTimingService
 * @package App\Services\Booking
 */
class BookingTimingService
{
    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * BookingTimingService constructor.
     *
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Check if booking has available time
     * and can be confirmed by trainer
     *
     * @param Booking $booking
     * @return ExecResult
     * @throws IncorrectDateRange
     */
    public function timeIsAvailable(Booking $booking): ExecResult
    {
        $confirmedBookingsCount = $this->bookingRepository->getConfirmedInDatesRange(
            Carbon::parse($booking->start_time),
            Carbon::parse($booking->end_time)
        )->count();

        if ($confirmedBookingsCount === 0) {
            return ExecResult::instance()->setSuccess();
        }

        return ExecResult::instance()->setSuccess(false)->setMessage(__('errors.booking_time_busy'));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingCreateFormRequest;
use App\Models\Booking;
use App\Models\Schedule;
use App\Services\Booking\BookingAvailabilityChecker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingController
 *
 * @package App\Http\Controllers\API
 */
class BookingController extends Controller
{
    /**
     * @var BookingAvailabilityChecker
     */
    protected $bookingAvailabilityChecker;

    /**
     * BookingController constructor.
     *
     * @param BookingAvailabilityChecker $bookingAvailabilityChecker
     */
    public function __construct(BookingAvailabilityChecker $bookingAvailabilityChecker)
    {
        $this->bookingAvailabilityChecker = $bookingAvailabilityChecker;
    }

    /**
     * @param Schedule $schedule
     * @param BookingCreateFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(
        Schedule $schedule,
        BookingCreateFormRequest $request
    ) {
        if (Auth::user()->cant('createBooking', $schedule)) {
            return $this->forbidden('Can\'t create booking');
        }

        $startTime = Carbon::parse($request->post('start_time'));
        $endTime = Carbon::parse($request->post('end_time'));

        if (!$this->bookingAvailabilityChecker->isAvailable($schedule, $startTime, $endTime)) {
            return $this->error(200, [], 'Period is unavailable');
        }

        /**
         * @var Booking $booking
         */
        $booking = Booking::create(array_merge($request->all(), [
            'schedule_id' => $schedule->id,
            'status' => Booking::STATUS_INACTIVE,
        ]));

        return $this->success($booking->toArray());
    }
}

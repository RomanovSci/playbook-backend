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
     *
     * @OA\Post(
     *      path="/api/booking/create/{schedule_id}",
     *      tags={"Booking"},
     *      summary="Create booking",
     *      @OA\Parameter(
     *          name="schedule_id",
     *          description="Schedule id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "start_time": "Start booking time. Example: 2018-05-12 09:00:00",
     *                      "end_time": "End booking time. Example: 2018-05-12 17:59:59",
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Booking"
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Can't create booking || Period is unavailable"
     *      ),
     *      security={{"Bearer":{}}}
     * )
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
            return $this->forbidden('Period is unavailable');
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

    /**
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/booking/confirm/{booking_id}",
     *      tags={"Booking"},
     *      summary="Confirm booking",
     *      @OA\Parameter(
     *          name="booking_id",
     *          description="Booking id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Booking"
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Can't confirm booking"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function confirm(Booking $booking)
    {
        if (Auth::user()->cant('confirmBooking', $booking)) {
            return $this->forbidden('Can\'t confirm booking');
        }

        $booking->status = Booking::STATUS_ACTIVE;

        if (!$booking->update(['status'])) {
            return $this->error(200, [], 'Can\'t update booking');
        }

        return $this->success($booking->toArray());
    }
}

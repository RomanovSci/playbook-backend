<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingCreateFormRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingController
 * @package App\Http\Controllers\API
 */
class BookingController extends Controller
{
    /**
     * @var BookingService
     */
    protected $bookingService;

    /**
     * BookingController constructor.
     *
     * @param BookingService $bookingService
     */
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * @param string $bookableType
     * @param BookingCreateFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     *
     * @OA\Post(
     *      path="/api/booking/{type}/create",
     *      tags={"Booking"},
     *      summary="Create booking",
     *      @OA\Parameter(
     *          name="type",
     *          description="trainer or playground",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "start_time": "Start booking time. Example: 2018-05-12 09:00:00",
     *                      "end_time": "End booking time. Example: 2018-05-12 17:59:59",
     *                      "bookable_id": "Trainer or playground id"
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
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
     *          description="
     *              Can't create booking for myself
     *              Schedules for this time interval doesn't exists
     *              This time already reserved"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(string $bookableType, BookingCreateFormRequest $request)
    {
        /** @var User $creator */
        $creator = Auth::user();
        $bookableId = (int) $request->post('bookable_id');
        $canCreate = $this->bookingService->checkBookingRequest(
            Carbon::parse($request->post('start_time')),
            Carbon::parse($request->post('end_time')),
            $bookableType,
            $bookableId,
            $creator
        );

        if (!$canCreate['success']) {
            throw new ForbiddenHttpException($canCreate['message']);
        }

        /** @var Booking $booking */
        $booking = Booking::create([
            'start_time' => $request->post('start_time'),
            'end_time' => $request->post('end_time'),
            'bookable_type' => $bookableType,
            'bookable_id' => $bookableId,
            'creator_id' => $creator->id,
        ]);

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
     *          description="
     *              Success
     *              Booking already confirmed",
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
            throw new ForbiddenHttpException('Can\'t confirm booking');
        }

        if ($booking->status === Booking::STATUS_CONFIRMED) {
            return $this->error(200, $booking->toArray(), 'Booking already confirmed');
        }

        $booking->status = Booking::STATUS_CONFIRMED;
        $booking->update(['status']);

        return $this->success($booking->toArray());
    }
}

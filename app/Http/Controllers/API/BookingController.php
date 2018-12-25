<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingConfirmFormRequest;
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
     * @throws \App\Exceptions\Internal\IncorrectBookableType
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
    public function create(string $bookableType, BookingCreateFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $bookableId = (int) $request->post('bookable_id');
        $canCreate = $this->bookingService->canCreate($user, $bookableType, $bookableId);

        if (!$canCreate) {
            throw new ForbiddenHttpException();
        }

        $booking = $this->bookingService->create(
            Carbon::parse($request->post('start_time')),
            Carbon::parse($request->post('end_time')),
            $bookableType,
            $bookableId,
            $user
        );

        return $this->success($booking->toArray());
    }

    /**
     * @param BookingConfirmFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/booking/confirm",
     *      tags={"Booking"},
     *      summary="Confirm booking",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "booking_id": "Booking id"
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
     *          description="Can't confirm booking"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function confirm(BookingConfirmFormRequest $request)
    {
        /** @var Booking $booking */
        $booking = Booking::find($request->post('booking_id'));

        if (Auth::user()->cant('confirmBooking', $booking)) {
            throw new ForbiddenHttpException('Can\'t confirm booking');
        }

        $booking->status = Booking::STATUS_ACTIVE;

        if (!$booking->update(['status'])) {
            return $this->error(200, [], 'Can\'t update booking');
        }

        return $this->success($booking->toArray());
    }
}

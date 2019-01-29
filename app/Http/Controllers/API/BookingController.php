<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingCreateFormRequest;
use App\Http\Requests\Booking\BookingDeclineFormRequest;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
     * @param int $bookableId
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/booking/{type}/{id}",
     *      tags={"Booking"},
     *      summary="Get bookings for trainer or playground",
     *      @OA\Parameter(
     *          name="type",
     *          description="trainer or playground",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="trainer or playground id",
     *          in="path",
     *          required=false,
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(
     *                          allOf={
     *                              @OA\Schema(ref="#/components/schemas/Booking"),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="playground",
     *                                      type="object",
     *                                      ref="#/components/schemas/Playground"
     *                                  ),
     *                              ),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="creator",
     *                                      type="object",
     *                                      ref="#/components/schemas/User"
     *                                  ),
     *                              ),
     *                          }
     *                      ),
     *                  ),
     *              )
     *         )
     *      )
     * )
     */
    public function get(string $bookableType, int $bookableId)
    {
        if (Gate::denies('getBookingsList', [$bookableType, $bookableId])) {
            throw new ForbiddenHttpException('Can\'t get booking list');
        }

        return $this->success(
            BookingRepository::getByBookable($bookableType, $bookableId)
        );
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
     *                      "note": "Optional",
     *                      "bookable_id": "Trainer or playground id",
     *                      "playground_id": "Required if {type} = trainer"
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
     *              Schedules for this time interval doesn't exists
     *              This time already reserved"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(string $bookableType, BookingCreateFormRequest $request)
    {
        $bookableId = (int) $request->post('bookable_id');
        $checkResult = $this->bookingService->checkBookingRequest(
            Carbon::parse($request->post('start_time')),
            Carbon::parse($request->post('end_time')),
            $bookableType,
            $bookableId
        );

        if (!$checkResult['success']) {
            throw new ForbiddenHttpException($checkResult['message']);
        }

        /** @var Booking $booking */
        $booking = Booking::create(array_merge($request->all(), [
            'bookable_type' => $bookableType,
            'creator_id' => Auth::user()->id,
        ]));

        return $this->success($booking->toArray());
    }

    /**
     * @param Booking $booking
     * @param Request $request
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
     *              Status already set",
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
     *          description="Can't manage booking"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function confirm(Booking $booking, Request $request)
    {
        $canConfirm = $this->bookingService->canConfirm($booking);

        if (!$canConfirm['success']) {
            throw new ForbiddenHttpException($canConfirm['message']);
        }

        return $this->changeStatus($booking, $request, Booking::STATUS_CONFIRMED);
    }

    /**
     * @param Booking $booking
     * @param BookingDeclineFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/booking/decline/{booking_id}",
     *      tags={"Booking"},
     *      summary="Decline booking",
     *      @OA\Parameter(
     *          name="booking_id",
     *          description="Booking id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "note": "Booking note",
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="
     *              Success
     *              Status already set",
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
     *          description="Can't manage booking"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function decline(Booking $booking, BookingDeclineFormRequest $request)
    {
        return $this->changeStatus($booking, $request, Booking::STATUS_DECLINED);
    }

    /**
     * Change booking status
     *
     * @param Booking $booking
     * @param Request $request
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Booking $booking, Request $request, int $status)
    {
        if (Auth::user()->cant('manageBooking', $booking)) {
            throw new ForbiddenHttpException('Can\'t manage booking');
        }

        if ($booking->status === $status) {
            return $this->error(200, $booking->toArray(), 'Status already set');
        }

        $booking->status = $status;
        $booking->note = $request->post('note');
        $booking->update(['status', 'note']);

        return $this->success($booking->toArray());
    }
}

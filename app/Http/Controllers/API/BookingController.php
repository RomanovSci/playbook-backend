<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\BookingCreateFormRequest;
use App\Http\Requests\Booking\BookingDeclineFormRequest;
use App\Http\Requests\Common\TimeIntervalFormRequest;
use App\Jobs\SendSms;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
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
     * @param TimeIntervalFormRequest $request
     * @param string $bookableType
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/booking/{type}/{uuid}",
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
     *          name="uuid",
     *          description="trainer or playground uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          description="Records limit. Max: 100",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="start_time",
     *          description="Start time. Example: 2018-05-13 09:00:00",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="end_time",
     *          description="End time. Example: 2018-05-13 17:00:00",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="string")
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
     *                                      property="bookable",
     *                                      type="object",
     *                                      ref="#/components/schemas/User"
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
    public function get(TimeIntervalFormRequest $request, string $bookableType, string $uuid)
    {
        if (Gate::denies('getBookingsList', [$bookableType, $uuid])) {
            throw new ForbiddenHttpException(__('errors.cant_get_bookings'));
        }

        return $this->success(
            BookingRepository::getByBookable(
                Carbon::parse($request->get('start_time')),
                Carbon::parse($request->get('end_time')),
                $request->get('limit'),
                $request->get('offset'),
                $bookableType,
                $uuid
            )
        );
    }

    /**
     * @param TimeIntervalFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/booking/all",
     *      tags={"Booking"},
     *      summary="Get all bookings for user",
     *      @OA\Parameter(
     *          name="limit",
     *          description="Records limit. Max: 100",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="start_time",
     *          description="Start time. Example: 2018-05-13 09:00:00",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="end_time",
     *          description="End time. Example: 2018-05-13 17:00:00",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="string")
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
     *                                      property="bookable",
     *                                      type="object",
     *                                      ref="#/components/schemas/User"
     *                                  ),
     *                              ),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="playground",
     *                                      type="object",
     *                                      ref="#/components/schemas/Playground"
     *                                  ),
     *                              )
     *                          }
     *                      ),
     *                  ),
     *              )
     *         )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function getUserBookings(TimeIntervalFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->success(
            BookingRepository::getByCreator(
                Carbon::parse($request->get('start_time')),
                Carbon::parse($request->get('end_time')),
                $request->get('limit'),
                $request->get('offset'),
                $user
            )
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
     *                      "bookable_uuid": "Trainer or playground uuid",
     *                      "playground_uuid": "Required if {type} = trainer"
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
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/Booking"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="bookable",
     *                                  type="object",
     *                                  ref="#/components/schemas/User"
     *                              ),
     *                          )
     *                      }
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
        $bookableUuid = $request->post('bookable_uuid');
        $result = $this->bookingService->getBookingPrice(
            Carbon::parse($request->post('start_time')),
            Carbon::parse($request->post('end_time')),
            $bookableType,
            $bookableUuid
        );

        if (!$result['success']) {
            throw new ForbiddenHttpException($result['message']);
        }

        /** @var Booking $booking */
        $booking = Booking::create(array_merge($request->all(), [
            'bookable_type' => $bookableType,
            'creator_uuid' => Auth::user()->uuid,
            'price' => $result['data']['price'],
            'currency' => $result['data']['currency'],
        ]));

        if ($bookableType === User::class && $bookableUuid !== Auth::user()->uuid) {
            SendSms::dispatch(
                UserRepository::getByUuid($bookableUuid)->phone,
                __('sms.booking.create')
            )->onConnection('redis');
        }

        return $this->success($booking->toArray());
    }

    /**
     * @param Booking $booking
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/booking/confirm/{booking_uuid}",
     *      tags={"Booking"},
     *      summary="Confirm booking",
     *      @OA\Parameter(
     *          name="booking_uuid",
     *          description="Booking uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
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
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/Booking"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="bookable",
     *                                  type="object",
     *                                  ref="#/components/schemas/User"
     *                              ),
     *                          )
     *                      }
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
    public function confirm(Booking $booking)
    {
        $canConfirm = $this->bookingService->canConfirm($booking);

        if (!$canConfirm['success'] || Auth::user()->cant('confirmBooking', $booking)) {
            throw new ForbiddenHttpException($canConfirm['message'] ?: __('errors.cant_confirm_booking'));
        }

        $changeStatusResult = $this->bookingService->changeBookingStatus(
            $booking,
            Booking::STATUS_CONFIRMED
        );

        if (!$changeStatusResult['success']) {
            $this->error(200, $booking->toArray(), $changeStatusResult['message']);
        }

        return $this->success($booking->toArray());
    }

    /**
     * @param Booking $booking
     * @param BookingDeclineFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/booking/decline/{booking_uuid}",
     *      tags={"Booking"},
     *      summary="Decline booking",
     *      @OA\Parameter(
     *          name="booking_uuid",
     *          description="Booking uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
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
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/Booking"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="bookable",
     *                                  type="object",
     *                                  ref="#/components/schemas/User"
     *                              ),
     *                          )
     *                      }
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
        if (Auth::user()->cant('declineBooking', $booking)) {
            throw new ForbiddenHttpException(__('errors.cant_decline_booking'));
        }

        $changeStatusResult = $this->bookingService->changeBookingStatus(
            $booking,
            Booking::STATUS_DECLINED,
            $request->post('note')
        );

        if (!$changeStatusResult['success']) {
            $this->error(200, $booking->toArray(), $changeStatusResult['message']);
        }

        return $this->success($booking->toArray());
    }
}

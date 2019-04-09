<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Exceptions\Internal\IncorrectDateRange;
use App\Helpers\BookingHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingFormRequest;
use App\Http\Requests\Booking\DeclineBookingFormRequest;
use App\Http\Requests\Common\TimeIntervalFormRequest;
use App\Models\Booking;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Services\Booking\ChangeBookingStatusService;
use App\Services\Booking\CreateBookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class BookingController
 * @package App\Http\Controllers\API
 */
class BookingController extends Controller
{
    /**
     * @param TimeIntervalFormRequest $request
     * @param string $bookableType
     * @param string $uuid
     * @return JsonResponse
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
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="equipments_rent",
     *                                      type="array",
     *                                      @OA\Items(
     *                                          allOf={
     *                                              @OA\Schema(
     *                                                  @OA\Property(
     *                                                      property="count",
     *                                                      type="integer",
     *                                                  ),
     *                                                  @OA\Property(
     *                                                      property="equipment",
     *                                                      type="object",
     *                                                      allOf={
     *                                                          @OA\Schema(
     *                                                              @OA\Property(
     *                                                                  property="uuid",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="name",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="price_per_hour",
     *                                                                  type="integer"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="currency",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="availability",
     *                                                                  type="integer"
     *                                                              ),
     *                                                          )
     *                                                      }
     *                                                  ),
     *                                              )
     *                                          }
     *                                      )
     *                                  ),
     *                              )
     *                          }
     *                      ),
     *                  ),
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "start_time": {
     *                              "The start time field is required."
     *                          },
     *                          "end_time": {
     *                              "The end time field is required."
     *                          },
     *                          "limit": {
     *                              "The limit field is required."
     *                          },
     *                          "offset": {
     *                              "The offset field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
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
     * @return JsonResponse
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
     *                              ),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="equipments_rent",
     *                                      type="array",
     *                                      @OA\Items(
     *                                          allOf={
     *                                              @OA\Schema(
     *                                                  @OA\Property(
     *                                                      property="count",
     *                                                      type="integer",
     *                                                  ),
     *                                                  @OA\Property(
     *                                                      property="equipment",
     *                                                      type="object",
     *                                                      allOf={
     *                                                          @OA\Schema(
     *                                                              @OA\Property(
     *                                                                  property="uuid",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="name",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="price_per_hour",
     *                                                                  type="integer"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="currency",
     *                                                                  type="string"
     *                                                              ),
     *                                                              @OA\Property(
     *                                                                  property="availability",
     *                                                                  type="integer"
     *                                                              ),
     *                                                          )
     *                                                      }
     *                                                  ),
     *                                              )
     *                                          }
     *                                      )
     *                                  ),
     *                              )
     *                          }
     *                      ),
     *                  ),
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "start_time": {
     *                              "The start time field is required."
     *                          },
     *                          "end_time": {
     *                              "The end time field is required."
     *                          },
     *                          "limit": {
     *                              "The limit field is required."
     *                          },
     *                          "offset": {
     *                              "The offset field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
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
     * @param CreateBookingFormRequest $request
     * @param CreateBookingService $createBookingService
     * @return JsonResponse
     * @throws IncorrectDateRange
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
     *                  type="object",
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="note",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="bookable_uuid",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="playground_uuid",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="players_count",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="equipments",
     *                      type="array",
     *                      @OA\Items(
     *                          allOf={
     *                              @OA\Schema(
     *                                  type="object",
     *                                  @OA\Property(
     *                                      property="uuid",
     *                                      type="string",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="count",
     *                                      type="integer"
     *                                  ),
     *                              )
     *                          }
     *                      )
     *                  ),
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
     *                      property="data",
     *                      type="object",
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/Booking"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="equipments_rent",
     *                                  type="array",
     *                                  @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(
     *                                              @OA\Property(
     *                                                  property="count",
     *                                                  type="integer",
     *                                              ),
     *                                              @OA\Property(
     *                                                  property="equipment",
     *                                                  type="object",
     *                                                  allOf={
     *                                                      @OA\Schema(
     *                                                          @OA\Property(
     *                                                              property="uuid",
     *                                                              type="string"
     *                                                          ),
     *                                                          @OA\Property(
     *                                                              property="name",
     *                                                              type="string"
     *                                                          ),
     *                                                          @OA\Property(
     *                                                              property="price_per_hour",
     *                                                              type="integer"
     *                                                          ),
     *                                                          @OA\Property(
     *                                                              property="currency",
     *                                                              type="string"
     *                                                          ),
     *                                                          @OA\Property(
     *                                                              property="availability",
     *                                                              type="integer"
     *                                                          ),
     *                                                      )
     *                                                  }
     *                                              ),
     *                                          )
     *                                      }
     *                                  )
     *                              ),
     *                          ),
     *                      }
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "start_time": {
     *                              "The start time field is required."
     *                          },
     *                          "end_time": {
     *                              "The end time field is required."
     *                          },
     *                          "bookable_uuid": {
     *                              "The bookable_uuid field is required."
     *                          },
     *                          "playground_uuid": {
     *                              "The playground_uuid field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(
        string $bookableType,
        CreateBookingFormRequest $request,
        CreateBookingService $createBookingService
    ) {
        /** @var User $user */
        $user = Auth::user();
        $result = $createBookingService->run($user, $bookableType, $request->all());

        if (!$result->getSuccess()) {
            throw new ForbiddenHttpException($result->getMessage());
        }

        return $this->success($result->getData());
    }

    /**
     * @param Booking $booking
     * @param ChangeBookingStatusService $changeBookingStatusService
     * @return JsonResponse
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
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function confirm(Booking $booking, ChangeBookingStatusService $changeBookingStatusService)
    {
        $checkAvailabilityResult = BookingHelper::timeIsAvailable($booking);

        if (!$checkAvailabilityResult->getSuccess() || Auth::user()->cant('confirmBooking', $booking)) {
            throw new ForbiddenHttpException(
                $checkAvailabilityResult->getMessage() ?: __('errors.cant_confirm_booking')
            );
        }

        $changeStatusResult = $changeBookingStatusService->run(
            $booking,
            Booking::STATUS_CONFIRMED
        );

        if (!$changeStatusResult->getSuccess()) {
            $this->error($changeStatusResult->getMessage(), $booking->toArray());
        }

        return $this->success($booking->toArray());
    }

    /**
     * @param Booking $booking
     * @param DeclineBookingFormRequest $request
     * @param ChangeBookingStatusService $changeBookingStatusService
     * @return JsonResponse
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
     *                              @OA\Property(
     *                                  property="creator",
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
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "note": {
     *                              "The note field is required."
     *                          },
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function decline(
        Booking $booking,
        DeclineBookingFormRequest $request,
        ChangeBookingStatusService $changeBookingStatusService
    ) {
        if (Auth::user()->cant('declineBooking', $booking)) {
            throw new ForbiddenHttpException(__('errors.cant_decline_booking'));
        }

        $changeStatusResult = $changeBookingStatusService->run(
            $booking,
            Booking::STATUS_DECLINED,
            $request->post('note')
        );

        if (!$changeStatusResult->getSuccess()) {
            $this->error($changeStatusResult->getMessage(), $booking->toArray());
        }

        return $this->success($booking->toArray());
    }
}

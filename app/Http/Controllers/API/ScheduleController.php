<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Exceptions\Internal\IncorrectDateRange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\TimeIntervalFormRequest;
use App\Http\Requests\Schedule\CreateScheduleFormRequest;
use App\Http\Requests\Schedule\EditScheduleFormRequest;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ScheduleRepository;
use App\Services\Schedule\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ScheduleController
 * @package App\Http\Controllers\API
 */
class ScheduleController extends Controller
{
    /**
     * @param TimeIntervalFormRequest $request
     * @param ScheduleRepository $scheduleRepository
     * @param BookingRepository $bookingRepository
     * @param string $schedulableType
     * @param string $uuid
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/schedules/{type}/{uuid}",
     *      tags={"Schedule"},
     *      summary="Get schedules for trainer or playground",
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
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          description="Records limit. Max: 100",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="query",
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
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(
     *                          allOf={
     *                              @OA\Schema(ref="#/components/schemas/Schedule"),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="confirmed_bookings",
     *                                      type="array",
     *                                      @OA\Items(ref="#/components/schemas/Booking")
     *                                  ),
     *                              ),
     *                          }
     *                      )
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
     * )
     */
    public function get(
        TimeIntervalFormRequest $request,
        ScheduleRepository $scheduleRepository,
        BookingRepository $bookingRepository,
        string $schedulableType,
        string $uuid = null
    ): JsonResponse {
        $schedules = $scheduleRepository->getBetween(
            Carbon::parse($request->get('start_time')),
            Carbon::parse($request->get('end_time')),
            (int) $request->get('limit'),
            (int) $request->get('offset'),
            $schedulableType,
            $uuid
        );

        /**
         * Append confirmed bookings to each schedule
         * @var Schedule $schedule
         */
        foreach ($schedules as $schedule) {
            $schedule->setAttribute('confirmed_bookings', $bookingRepository->getConfirmedForSchedule($schedule));
        }

        return $this->success($schedules);
    }

    /**
     * @param CreateScheduleFormRequest $request
     * @param string $schedulableType
     * @param ScheduleService $scheduleService
     * @return JsonResponse
     *
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/schedules/{type}",
     *      tags={"Schedule"},
     *      summary="Create schedule",
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
     *                  required={
     *                      "dates",
     *                      "price_per_hour",
     *                      "currency",
     *                      "playgrounds",
     *                  },
     *                  example={
     *                      "dates": {{
     *                          "start_time": "2019-01-01 00:00:00",
     *                          "end_time": "2019-01-01 01:00:00",
     *                      }},
     *                      "price_per_hour": "7000",
     *                      "currency": "USD",
     *                      "playgrounds": {
     *                          "0000000-1111-2222-3333-444444444444",
     *                          "Playgrounds uuids. If type=playground, array should contains only 1 uuid"
     *                      }
     *                  },
     *                  @OA\Property(
     *                      property="dates",
     *                      type="array",
     *                      @OA\Items(
     *                          allOf={
     *                              @OA\Schema(
     *                                  type="object",
     *                                  required={
     *                                      "start_time",
     *                                      "end_time",
     *                                  },
     *                                  @OA\Property(
     *                                      property="start_time",
     *                                      type="string",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="end_time",
     *                                      type="string"
     *                                  ),
     *                              )
     *                          }
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="price_per_hour",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="playgrounds",
     *                      type="array",
     *                      @OA\Items()
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Created",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Schedule")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "message": "Validation error",
     *                      "data": {
     *                          "dates": {
     *                              "The dates field is required."
     *                          },
     *                          "price_per_hour": {
     *                              "The price per hour field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
     *                          },
     *                          "playgrounds": {
     *                              "The playgrounds field is required."
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
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(
        CreateScheduleFormRequest $request,
        string $schedulableType,
        ScheduleService $scheduleService
    ): JsonResponse {
        /** @var User $schedulable */
        $schedulable = Auth::user();

        /**
         * Restrict create schedule
         * for admin and organization-admin
         */
        if ($schedulableType === User::class && !$schedulable->hasRole(['trainer'])) {
            throw new ForbiddenHttpException(__('errors.only_trainer_can_create_schedule'));
        }

        if ($schedulableType === Playground::class) {
            $schedulable = Playground::find($request->post('playgrounds')[0]);

            /**
             * We don't need validate createSchedule permission
             * for $schedulable = Auth::user(), because this action is available
             * for only trainer, organization-admin and system admin
             */
            if (Auth::user()->cant('createSchedule', $schedulable)) {
                throw new ForbiddenHttpException(__('errors.cant_create_schedule_for_playground'));
            }
        }

        $createResult = $scheduleService->create($schedulable, $request->all());
        return $this->created($createResult->getData('schedules'));
    }

    /**
     * @param Schedule $schedule
     * @param EditScheduleFormRequest $request
     * @param ScheduleService $scheduleService
     * @return JsonResponse
     *
     * @throws IncorrectDateRange
     *
     * @OA\Put(
     *      path="/api/schedules/{schedule_uuid}",
     *      tags={"Schedule"},
     *      summary="Edit schedule",
     *      @OA\Parameter(
     *          name="schedule_uuid",
     *          description="Schedule uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "start_time",
     *                      "end_time",
     *                      "price_per_hour",
     *                      "currency",
     *                      "playgrounds",
     *                  },
     *                  example={
     *                      "start_time": "2019-01-01 00:00:00",
     *                      "end_time": "2019-01-01 01:00:00",
     *                      "price_per_hour": "7000",
     *                      "currency": "USD",
     *                      "playgrounds": {"0000000-1111-2222-3333-444444444444"}
     *                  },
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="price_per_hour",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="playgrounds",
     *                      type="array",
     *                      @OA\Items()
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
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Schedule"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "message": "Validation error",
     *                      "data": {
     *                          "start_time": {
     *                              "The start time field is required."
     *                          },
     *                          "end_time": {
     *                              "The end time field is required."
     *                          },
     *                          "price_per_hour": {
     *                              "The price per hout field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
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
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function edit(
        Schedule $schedule,
        EditScheduleFormRequest $request,
        ScheduleService $scheduleService
    ): JsonResponse {
        if (Auth::user()->cant('manageSchedule', $schedule)) {
            throw new ForbiddenHttpException(__('errors.cant_manage_schedule'));
        }

        return $this->success(
            $scheduleService->edit($schedule, $request->all())->getData('schedule')
        );
    }

    /**
     * @param Schedule $schedule
     * @return JsonResponse
     *
     * @throws \Exception
     *
     * @OA\Delete(
     *      path="/api/schedules/{schedule_uuid}",
     *      tags={"Schedule"},
     *      summary="Delete schedule",
     *      @OA\Parameter(
     *          name="schedule_uuid",
     *          description="Schedule uuid",
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
     *                      property="message",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
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
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function delete(Schedule $schedule): JsonResponse
    {
        if (Auth::user()->cant('manageSchedule', $schedule)) {
            throw new ForbiddenHttpException(__('errors.cant_manage_schedule'));
        }

        $schedule->delete();
        return $this->success();
    }
}

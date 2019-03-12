<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\TimeIntervalFormRequest;
use App\Http\Requests\Schedule\ScheduleCreateFormRequest;
use App\Http\Requests\Schedule\ScheduleEditFormRequest;
use App\Models\Playground;
use App\Models\Schedule\Schedule;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleService;
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
     * @param string $schedulableType
     * @param string $uuid
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/schedule/{type}/{uuid}",
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
     *                              @OA\Schema(ref="#/components/schemas/Schedule"),
     *                              @OA\Schema(
     *                                  @OA\Property(
     *                                      property="confirmed_bookings",
     *                                      type="array",
     *                                      @OA\Items(
     *                                          allOf={
     *                                              @OA\Schema(ref="#/components/schemas/Booking"),
     *                                              @OA\Schema(
     *                                                  @OA\Property(
     *                                                      property="creator",
     *                                                      type="object",
     *                                                      ref="#/components/schemas/User",
     *                                                  )
     *                                              ),
     *                                              @OA\Schema(
     *                                                  @OA\Property(
     *                                                      property="bookable",
     *                                                      type="object",
     *                                                      ref="#/components/schemas/User"
     *                                                  ),
     *                                              ),
     *                                          }
     *                                      )
     *                                  ),
     *                              ),
     *                          }
     *                      )
     *                  ),
     *              )
     *         )
     *      )
     * )
     */
    public function get(TimeIntervalFormRequest $request, string $schedulableType, string $uuid = null)
    {
        $schedules = ScheduleRepository::getBetween(
            Carbon::parse($request->get('start_time')),
            Carbon::parse($request->get('end_time')),
            $request->get('limit'),
            $request->get('offset'),
            $schedulableType,
            $uuid
        );

        /**
         * Append confirmed bookings to each schedule
         * @var Schedule $schedule
         */
        foreach ($schedules as $schedule) {
            $schedule->setAttribute(
                'confirmed_bookings',
                BookingRepository::getConfirmedForSchedule($schedule)
            );
        }

        return $this->success($schedules);
    }

    /**
     * @param ScheduleCreateFormRequest $request
     * @param string $schedulableType
     * @return JsonResponse
     *
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/schedule/{type}/create",
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
     *                  example={
     *                      "dates": "Array with date objects. Example: [{start_time: 2018-05-12 17:00:00, end_time: 2018-05-12 19:00:00}]",
     *                      "price_per_hour": "Price per hour in cents. Example: 7000. (70RUB)",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB",
     *                      "playgrounds": "Array of playgrounds uuids. If type=playground, array should contains only 1 uuid"
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Schedule")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(ScheduleCreateFormRequest $request, string $schedulableType)
    {
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

        $createResult = ScheduleService::create($schedulable, $request->all());
        return $this->success($createResult->getData('schedules'));
    }

    /**
     * @param Schedule $schedule
     * @param ScheduleEditFormRequest $request
     * @return JsonResponse
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     *
     * @OA\Post(
     *      path="/api/schedule/edit/{schedule_uuid}",
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
     *                  example={
     *                      "start_time": "Period start time. Example: 2018-01-01 09:00:00",
     *                      "end_time": "Period end time. Example: 2018-01-01 17:00:00",
     *                      "price_per_hour": "Price per hour in cents. Example: 7000. (70RUB)",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB"
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
     *                      ref="#/components/schemas/Schedule"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid schedule uuid"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function edit(Schedule $schedule, ScheduleEditFormRequest $request)
    {
        if (Auth::user()->cant('manageSchedule', $schedule)) {
            throw new ForbiddenHttpException(__('errors.cant_manage_schedule'));
        }

        return $this->success(
            ScheduleService::edit($schedule, $request->all())->getData('schedule')
        );
    }

    /**
     * @param Schedule $schedule
     * @return JsonResponse
     *
     * @throws \Exception
     *
     * @OA\Delete(
     *      path="/api/schedule/delete/{schedule_uuid}",
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
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid schedule uuid"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function delete(Schedule $schedule)
    {
        if (Auth::user()->cant('manageSchedule', $schedule)) {
            throw new ForbiddenHttpException(__('errors.cant_manage_schedule'));
        }

        $schedule->delete();
        return $this->success();
    }
}

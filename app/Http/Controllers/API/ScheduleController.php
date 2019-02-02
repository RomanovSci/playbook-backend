<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\TimeIntervalFormRequest;
use App\Http\Requests\Schedule\ScheduleCreateFormRequest;
use App\Http\Requests\Schedule\ScheduleEditFormRequest;
use App\Models\Playground;
use App\Models\Schedule;
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
    protected $scheduleService;

    /**
     * ScheduleController constructor.
     *
     * @param ScheduleService $scheduleService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * @param TimeIntervalFormRequest $request
     * @param string $schedulableType
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/schedule/{type}/{id}",
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
     *          name="id",
     *          description="trainer or playground id",
     *          in="path",
     *          required=false,
     *          @OA\Schema(type="integer")
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
     *                                              )
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
    public function get(TimeIntervalFormRequest $request, string $schedulableType, int $id = null)
    {
        $schedules = ScheduleRepository::getByDateRange(
            Carbon::parse($request->get('start_time')),
            Carbon::parse($request->get('end_time')),
            $request->get('limit'),
            $request->get('offset'),
            $schedulableType,
            $id
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
     * @throws \App\Exceptions\Internal\IncorrectDateRange
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
     *                      "dates": "Array with dates of periods. Example: [2018-05-12, 2018-05-13]",
     *                      "start_time": "Period start time. Example: 09:00:00",
     *                      "end_time": "Period end time. Example: 17:00:00",
     *                      "price_per_hour": "Price per hour in cents. Example: 7000. (70RUB)",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB",
     *                      "playgrounds": "Array of playgrounds id.If type=playground, array should contains only 1 id"
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
        $isForTrainer = $schedulableType === User::class;
        $schedulable = Auth::user();
        $requestData = $request->all();

        /**
         * Restrict create schedule
         * for admin and organization-admin
         */
        if ($isForTrainer && !$schedulable->hasRole(['trainer'])) {
            throw new ForbiddenHttpException(__('errors.only_trainer_can_create_schedule'));
        }

        if (!$isForTrainer) {
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

        $creationResult = $this->scheduleService->create($schedulable, $requestData);
        return $this->success($creationResult);
    }

    /**
     * @param Schedule $schedule
     * @param ScheduleEditFormRequest $request
     * @return JsonResponse
     *
     * @throws \App\Exceptions\Internal\IncorrectDateRange
     *
     * @OA\Post(
     *      path="/api/schedule/edit/{schedule_id}",
     *      tags={"Schedule"},
     *      summary="Edit schedule",
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
     *          description="Invalid schedule id"
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
            $this->scheduleService->edit($schedule, $request->all())
        );
    }

    /**
     * @param Schedule $schedule
     * @return JsonResponse
     *
     * @throws \Exception
     *
     * @OA\Delete(
     *      path="/api/schedule/delete/{schedule_id}",
     *      tags={"Schedule"},
     *      summary="Delete schedule",
     *      @OA\Parameter(
     *          name="schedule_id",
     *          description="Schedule id",
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
     *          description="Invalid schedule id"
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

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\ScheduleGetFormRequest;
use App\Http\Requests\Schedule\ScheduleCreateFormRequest;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param ScheduleGetFormRequest $request
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
     *                      @OA\Items(ref="#/components/schemas/Schedule")
     *                  )
     *              )
     *         )
     *      )
     * )
     */
    public function get(ScheduleGetFormRequest $request, string $schedulableType, int $id = null)
    {
        $schedules = ScheduleRepository::getActiveInRange(
            Carbon::parse($request->get('start_time')),
            Carbon::parse($request->get('end_time')),
            $schedulableType,
            $id
        );
        return $this->success($schedules->toArray());
    }

    /**
     * @param string $schedulableType
     * @param ScheduleCreateFormRequest $request
     * @return JsonResponse
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

        /**
         * Restrict create schedule
         * for admin and organization-admin
         */
        if ($isForTrainer && !$schedulable->hasRole(['trainer'])) {
            return $this->forbidden('Only trainer can create schedule.');
        }

        if (!$isForTrainer) {
            $schedulable = Playground::find($request->post('playgrounds')[0]);

            /**
             * We don't need validate createSchedule permission
             * for $schedulable = Auth::user(), because this action is available
             * for only trainer, organization-admin and system admin
             */
            if (Auth::user()->cant('createSchedule', $schedulable)) {
                return $this->forbidden();
            }
        }

        $schedules = $this->scheduleService->create($schedulable, $request->all());
        return $this->success($schedules);
    }

    /**
     * @param Schedule $schedule
     * @return JsonResponse
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
        if (Auth::user()->cant('deleteSchedule', $schedule)) {
            return $this->forbidden();
        }

        $schedule->delete();
        return $this->success();
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\ScheduleGetFormRequest;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use App\Services\Schedule\ScheduleCreatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Class ScheduleController
 *
 * @package App\Http\Controllers\API
 */
class ScheduleController extends Controller
{
    protected $scheduleCreatorService;

    /**
     * ScheduleController constructor.
     *
     * @param ScheduleCreatorService $scheduleCreatorService
     */
    public function __construct(ScheduleCreatorService $scheduleCreatorService)
    {
        $this->scheduleCreatorService = $scheduleCreatorService;
    }

    /**
     * @param string $type
     * @param ScheduleGetFormRequest $request
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/schedule/{type}",
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
    public function get(string $type, ScheduleGetFormRequest $request)
    {
        $schedules = ScheduleRepository::getActiveByTypeInRange(
            Schedule::SCHEDULE_TYPES[$type],
            Carbon::parse($request->get('start_time')),
            Carbon::parse($request->get('end_time'))
        );
        return $this->success($schedules->toArray());
    }

    /**
     * Create schedule for trainer
     *
     * @param string
     * @param Request $request
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
     *                      "playground_id": "Playground id. Required if type = playground"
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
    public function create(string $type, Request $request)
    {
        $isForTrainer = $type === 'trainer';
        $validator = Validator::make($request->all(), array_merge(
            [
                'dates' => 'required|array',
                'dates.*' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s',
                'price_per_hour' => 'required|numeric',
                'currency' => 'required|string|uppercase|currency',
            ],
            $isForTrainer ? [] : ['playground_id' => 'required|numeric|exists:playgrounds,id']
        ));

        if ($validator->fails()) {
            return response()->json($validator->errors(), self::CODE_VALIDATION_ERROR);
        }

        /** @var User $schedulable */
        $schedulable = Auth::user();

        if (!$isForTrainer) {
            $schedulable = Playground::find($request->post('playground_id'));

            if (Auth::user()->cant('createSchedule', $schedulable)) {
                return $this->forbidden();
            }
        }

        $schedules = $this->scheduleCreatorService->create($schedulable, $request->all());
        return $this->success($schedules);
    }
}

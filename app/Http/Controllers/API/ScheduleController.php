<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\ScheduleGetFormRequest;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Schedule\ScheduleCreateFormRequest;

/**
 * Class ScheduleController
 *
 * @package App\Http\Controllers\API
 */
class ScheduleController extends Controller
{
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
     * @param ScheduleCreateFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/schedule/trainer/create",
     *      tags={"Schedule"},
     *      summary="Create trainer schedule",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "dates": "Array with dates of periods. Example: [2018-05-12, 2018-05-13]",
     *                      "start_time": "Period start time. Example: 09:00:00",
     *                      "end_time": "Period end time. Example: 17:00:00",
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
    public function createForTrainer(ScheduleCreateFormRequest $request)
    {
        /**
         * @var User $user
         */
        $schedules = [];
        $requestData = $request->all();
        $requestData['price_per_hour'] = money($requestData['price_per_hour'], $requestData['currency'])->getAmount();
        $user = Auth::user();

        foreach ($requestData['dates'] as $index => $date) {
            /**
             * @var Schedule $trainerSchedule
             */
            $trainerSchedule = Schedule::create(array_merge($requestData, [
                'start_time' => $date . ' ' . $requestData['start_time'],
                'end_time' => $date . ' ' . $requestData['end_time'],
                'schedulable_id' => $user->id,
                'schedulable_type' => User::class
            ]));
            $schedules[] = $trainerSchedule->toArray();
        }

        return $this->success($schedules);
    }

    /**
     * @param ScheduleCreateFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/schedule/playground/create",
     *      tags={"Schedule"},
     *      summary="Create playground schedule",
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
     *                      "playground_id": "Playground id"
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
    public function createForPlayground(ScheduleCreateFormRequest $request)
    {
        /** @var Playground $playground */
        $playground = Playground::find($request->post('playground_id'));

        if (Auth::user()->cant('createSchedule', $playground)) {
            return $this->forbidden();
        }

        $schedules = [];
        $requestData = $request->all();
        $requestData['price_per_hour'] = money(
            $requestData['price_per_hour'],
            $requestData['currency']
        )->getAmount();

        foreach ($requestData['dates'] as $index => $date) {
            /**
             * @var Schedule $trainerSchedule
             */
            $trainerSchedule = Schedule::create(array_merge($requestData, [
                'start_time' => $date . ' ' . $requestData['start_time'],
                'end_time' => $date . ' ' . $requestData['end_time'],
                'schedulable_id' => $playground->id,
                'schedulable_type' => Playground::class
            ]));
            $schedules[] = $trainerSchedule->toArray();
        }

        return $this->success($schedules);
    }
}

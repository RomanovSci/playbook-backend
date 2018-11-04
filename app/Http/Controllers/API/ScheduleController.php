<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
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
     * Create trainer schedule
     *
     * @param ScheduleCreateFormRequest $request
     * @return JsonResponse
     */
    public function createForTrainer(
        ScheduleCreateFormRequest $request
    ) {
        $data = $request->all();
        $data['price_per_hour'] = money($data['price_per_hour'], $data['currency'])->getAmount();

        /**
         * @var User $user
         * @var Schedule $trainerSchedule
         */
        $user = Auth::user();
        $trainerSchedule = Schedule::create($data);
        $trainerSchedule->users()->save($user);

        return $this->success($trainerSchedule->toArray());
    }

    /**
     * Create playground schedule
     *
     * @param Playground $playground
     * @param ScheduleCreateFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/create-for-playground/{playground_id}",
     *      tags={"Schedule"},
     *      summary="Create trainer information",
     *      @OA\Parameter(
     *          name="playground_id",
     *          description="Playground id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "start_time": "Period start. Example: 2018-05-30 09:00:00",
     *                      "end_time": "Period end. Example: 2018-05-30 17:00:00",
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
     *              @OA\Schema(ref="#/components/schemas/Schedule")
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function createForPlayground(
        Playground $playground,
        ScheduleCreateFormRequest $request
    ) {
        if (Auth::user()->cant('createSchedule', $playground)) {
            return $this->forbidden();
        }

        $data = $request->all();
        $data['price_per_hour'] = money($data['price_per_hour'], $data['currency'])->getAmount();

        /** @var Schedule $playgroundSchedule */
        $playgroundSchedule = Schedule::create($data);
        $playgroundSchedule->playgrounds()->save($playground);

        return $this->success($playgroundSchedule->toArray());
    }
}

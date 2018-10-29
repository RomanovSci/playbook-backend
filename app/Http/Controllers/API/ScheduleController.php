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

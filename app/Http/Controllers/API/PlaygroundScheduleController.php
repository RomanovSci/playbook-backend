<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Playground;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PlaygroundSchedule\Create as PlaygroundScheduleCreateRequest;

class PlaygroundScheduleController extends Controller
{
    /**
     * Create playground pricing
     *
     * @param Playground $playground
     * @param PlaygroundScheduleCreateRequest $request
     * @return JsonResponse
     */
    public function create(
        Playground $playground,
        PlaygroundScheduleCreateRequest $request
    ) {
        if (Auth::user()->cant('createSchedule', $playground)) {
            return $this->forbidden();
        }

        /**
         * @var Schedule $playgroundSchedule
         */
        $playgroundSchedule = Schedule::create($request->all());
        $playgroundSchedule->playgrounds()->save($playground);

        return $this->success($playgroundSchedule->toArray());
    }
}

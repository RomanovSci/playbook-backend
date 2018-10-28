<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Playground;
use App\Models\PlaygroundSchedule;
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
         * @var PlaygroundSchedule $playgroundSchedule
         */
        $playgroundSchedule = PlaygroundSchedule::create(
            $request->all()
        );

        return $this->success($playgroundSchedule->toArray());
    }
}

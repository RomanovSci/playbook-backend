<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Playground;
use App\Models\PlaygroundRentPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PlaygroundRentPrice\Create as PlaygroundRentPriceCreateRequest;

class PlaygroundRentPriceController extends Controller
{
    /**
     * Create playground pricing
     *
     * @param Playground $playground
     * @param PlaygroundRentPriceCreateRequest $request
     * @return JsonResponse
     */
    public function create(
        Playground $playground,
        PlaygroundRentPriceCreateRequest $request
    ) {
        if (Auth::user()->cant('createRentPrice', $playground)) {
            return $this->forbidden();
        }

        /**
         * @var PlaygroundRentPrice $playgroundRentPrice
         */
        $playgroundRentPrice = PlaygroundRentPrice::create(
            $request->all()
        );

        return $this->success($playgroundRentPrice->toArray());
    }
}

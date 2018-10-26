<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Playground;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PlaygroundRentPriceController extends Controller
{
    /**
     * Create playground pricing
     *
     * @param Playground $playground
     * @return JsonResponse
     */
    public function create(Playground $playground)
    {
        if (Auth::user()->cant('createRentPrice', $playground)) {
            return $this->forbidden();
        }

        return $this->success();
    }
}

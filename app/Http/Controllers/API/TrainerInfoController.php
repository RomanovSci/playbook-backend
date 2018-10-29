<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerInfo\TrainerInfoFormRequest;

/**
 * Class TrainerInfoController
 *
 * @package App\Http\Controllers\API
 */
class TrainerInfoController extends Controller
{
    /**
     * Create trainer info
     *
     * @param TrainerInfoFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(TrainerInfoFormRequest $request)
    {
        return $this->success();
    }
}

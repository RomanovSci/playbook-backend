<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerInfo\TrainerInfoFormRequest;
use App\Models\TrainerInfo;
use Illuminate\Support\Facades\Auth;

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
        /**
         * @var TrainerInfo $trainerInfo
         */
        $trainerInfo = TrainerInfo::create(array_merge($request->all(), [
            'user_id' => Auth::user()->id,
        ]));
        return $this->success($trainerInfo->toArray());
    }
}

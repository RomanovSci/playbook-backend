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
     *
     * @OA\Post(
     *      path="/api/trainer-info/create",
     *      tags={"TrainerInfo"},
     *      summary="Create trainer information",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "about": "Short information about trainer",
     *                      "min_price": "Min price in cents. Example: 7000. (70RUB)",
     *                      "max_price": "Max price in cents.",
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
     *              @OA\Schema(ref="#/components/schemas/TrainerInfo")
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
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

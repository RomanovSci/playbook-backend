<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerInfo\TrainerInfoCreateFormRequest;
use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class TrainerInfoController
 *
 * @package App\Http\Controllers\API
 */
class TrainerInfoController extends Controller
{
    /**
     * @param TrainerInfoCreateFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
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
     *                      "playgrounds": "Array of playgrounds ids. Example: [1,2,3]",
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
     *                      type="object",
     *                      property="data",
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/TrainerInfo"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="playgrounds",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/Playground")
     *                              ),
     *                          ),
     *                      }
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(TrainerInfoCreateFormRequest $request)
    {
        /**
         * @var User $user
         * @var TrainerInfo $trainerInfo
         */
        $user = Auth::user();
        $playgroundIds = $request->post('playgrounds');

        DB::beginTransaction();

        try {
            $trainerInfo = TrainerInfo::create(array_merge($request->all(), [
                'user_id' => $user->id,
            ]));

            foreach ($playgroundIds as $playgroundId) {
                UserPlayground::create([
                    'user_id' => $user->id,
                    'playground_id' => $playgroundId
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success(array_merge($trainerInfo->toArray(), [
            'playgrounds' => $user->playgrounds()->get(),
        ]));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentRequestFormRequest;
use App\Models\TournamentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class TournamentRequestController
 * @package App\Http\Controllers\API
 */
class TournamentRequestController extends Controller
{
    /**
     * @param CreateTournamentRequestFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/api/tournament/request",
     *      tags={"Tournament"},
     *      summary="Create tournament request",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "tournament_uuid"
     *                  },
     *                  @OA\Property(
     *                      property="tournament_uuid",
     *                      type="string",
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
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
     *                      property="data",
     *                      type="object",
     *                      ref="#/components/schemas/TournamentRequest"
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "tournament_uuid": {
     *                              "The tournament_uuid field is required."
     *                          },
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(CreateTournamentRequestFormRequest $request)
    {
        /**
         * @var User $user
         * @var TournamentRequest $tournamentRequest
         */
        $user = Auth::user();
        $tournamentRequest = TournamentRequest::create(array_merge($request->all(), [
            'user_uuid' => $user->uuid,
        ]));

        return $this->success($tournamentRequest);
    }
}

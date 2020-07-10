<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TournamentPlayer\GetTournamentPlayerFormRequest;
use App\Models\TournamentPlayer;
use App\Repositories\TournamentPlayerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class TournamentPlayerController
 * @package App\Http\Controllers\API
 */
class TournamentPlayerController extends Controller
{
    /**
     * @param GetTournamentPlayerFormRequest $request
     * @param TournamentPlayerRepository $repository
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/tournament_players",
     *      tags={"Tournament"},
     *      summary="Get tournament players",
     *      @OA\Parameter(
     *          name="tournament_uuid",
     *          description="Tournament uuid",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/TournamentPlayer")
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function get(GetTournamentPlayerFormRequest $request, TournamentPlayerRepository $repository): JsonResponse
    {
        return $this->success($repository->whereArray($request->all())->get());
    }

    /**
     * @param TournamentPlayer $tournamentPlayer
     * @return JsonResponse
     * @throws \Exception
     *
     * @OA\Delete(
     *      path="/api/tournament_players/{tournament_player_uuid}",
     *      tags={"Tournament"},
     *      summary="Delete tournament players",
     *      @OA\Parameter(
     *          name="tournament_player_uuid",
     *          description="Tournament player uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
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
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function delete(TournamentPlayer $tournamentPlayer): JsonResponse
    {
        if (Auth::user()->cant('deletePlayer', $tournamentPlayer)) {
            throw new ForbiddenHttpException(__('errors.forbidden_delete_tournament_player'));
        }

        if ($tournamentPlayer->tournament->started_at) {
            return $this->error(__('errors.tournament_already_started'));
        }

        if (!$tournamentPlayer->delete()) {
            return $this->error(__('errors.cant_delete_tournament_player'));
        }

        return $this->success();
    }
}

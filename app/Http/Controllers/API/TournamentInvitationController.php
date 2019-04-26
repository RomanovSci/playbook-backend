<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentInvitationFormRequest;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use App\Repositories\TournamentInvitationRepository;
use App\Repositories\TournamentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

/**
 * Class TournamentInviteController
 * @package App\Http\Controllers\API
 */
class TournamentInvitationController extends Controller
{
    /**
     * @param CreateTournamentInvitationFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/tournament/invitation",
     *      tags={"Tournament"},
     *      summary="Create tournament invitation",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "tournament_uuid",
     *                      "invited_uuid",
     *                  },
     *                  @OA\Property(
     *                      property="tournament_uuid",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="invited_uuid",
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
     *                      ref="#/components/schemas/TournamentInvitation"
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
     *                          "invited_uuid": {
     *                              "The invited_uuid field is required."
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
    public function create(CreateTournamentInvitationFormRequest $request): JsonResponse
    {
        /**
         * @var User $user
         * @var Tournament $tournament
         */
        $user = Auth::user();
        $tournament = TournamentRepository::getByUuid($request->get('tournament_uuid'));

        if ($user->cant('invite', $tournament)) {
            throw new ForbiddenHttpException('Only tournament owner can invite user');
        }

        $tournamentInvitation = TournamentInvitationRepository::getByTournamentAndInvitedUuid(
            $tournament->uuid,
            $request->get('invited_uuid')
        );

        if ($tournamentInvitation) {
            return $this->error('Invitation already exists', $tournamentInvitation);
        }

        /** @var TournamentInvitation $tournamentInvitation */
        $tournamentInvitation = TournamentInvitation::create(array_merge($request->all(), [
            'inviter_uuid' => $user->uuid,
        ]));

        return $this->success($tournamentInvitation);
    }
}

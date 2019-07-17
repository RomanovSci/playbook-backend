<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\ApproveTournamentInvitationFormRequest;
use App\Http\Requests\Tournament\CreateTournamentInvitationFormRequest;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use App\Repositories\TournamentInvitationRepository;
use App\Repositories\TournamentRepository;
use Carbon\Carbon;
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
     *          response="201",
     *          description="Created",
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

        if ($user->cant('manage', $tournament)) {
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

        return $this->created($tournamentInvitation);
    }

    /**
     * @param ApproveTournamentInvitationFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/tournament/invitation/approve",
     *      tags={"Tournament"},
     *      summary="Approve tournament invitation",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "tournament_invitation_uuid"
     *                  },
     *                  @OA\Property(
     *                      property="tournament_invitation_uuid",
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
     *                          "tournament_invitation_uuid": {
     *                              "The tournament_invitation_uuid field is required."
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
    public function approve(ApproveTournamentInvitationFormRequest $request): JsonResponse
    {
        /**
         * @var User $user
         * @var TournamentInvitation $tournamentInvitation
         */
        $user = Auth::user();
        $tournamentInvitation = TournamentInvitationRepository::getByUuid(
            $request->get('tournament_invitation_uuid')
        );

        if (!$tournamentInvitation) {
            return $this->error('Can not find tournament invitation');
        }

        if ($user->cant('approve', $tournamentInvitation)) {
            throw new ForbiddenHttpException('Can not approve tournament invitation');
        }

        if ($tournamentInvitation->approved_at) {
            return $this->error('Tournament invitation already approved', $tournamentInvitation);
        }

        $tournamentInvitation->approved_at = Carbon::now()->format('Y-m-d H:i:s');
        $tournamentInvitation->update(['approved_at']);

        return $this->success($tournamentInvitation);
    }
}

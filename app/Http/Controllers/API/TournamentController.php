<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentFormRequest;
use App\Models\Tournament;
use App\Repositories\TournamentTypeRepository;
use App\Services\Tournament\CreateTournamentService;
use App\Services\Tournament\StartTournamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class TournamentController
 * @package App\Http\Controllers\API
 */
class TournamentController extends Controller
{
    /**
     * @param CreateTournamentFormRequest $request
     * @param CreateTournamentService $service
     * @return JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/tournaments",
     *      tags={"Tournament"},
     *      summary="Create tournament",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "title",
     *                      "sport",
     *                      "category",
     *                      "price",
     *                      "currency",
     *                      "tournament_type_uuid",
     *                  },
     *                  example={
     *                      "title": "Tournament",
     *                      "sport": "Tennis",
     *                      "category": "men",
     *                      "price": "2000",
     *                      "currency": "USD",
     *                      "tournament_type_uuid": "0000000-1111-2222-3333-444444444444",
     *                  },
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="sport",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="category",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="tournament_type_uuid",
     *                      type="stringx`"
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
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Tournament"
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
     *                      "message": "Validation error",
     *                      "data": {
     *                          "title": {
     *                              "The title field is required."
     *                          },
     *                          "sport": {
     *                              "The sport field is required."
     *                          },
     *                          "category": {
     *                              "The category field is required."
     *                          },
     *                          "price": {
     *                              "The price field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
     *                          },
     *                          "tournament_type_uuid": {
     *                              "The tournament type uuid field is required."
     *                          }
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
    public function create(CreateTournamentFormRequest $request, CreateTournamentService $service): JsonResponse
    {
        $createResult = $service->create(Auth::user(), $request->all());

        if (!$createResult->getSuccess()) {
            return $this->error($createResult->getMessage());
        }

        return $this->created($createResult->getData());
    }

    /**
     * @param Tournament $tournament
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/tournaments/{tournament_uuid}/start",
     *      tags={"Tournament"},
     *      summary="Start tournament",
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
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Tournament"
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
     *                      "message": "Validation error",
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
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Cant start tournament",
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
    public function start(Tournament $tournament): JsonResponse
    {
        if (Auth::user()->cant('startTournament', $tournament)) {
            throw new ForbiddenHttpException(__('errors.cant_start_tournament'));
        }

        if ($tournament->started_at) {
            return $this->error(__('errors.tournament_already_started'), $tournament);
        }

        $tournament->started_at = now();

        if (!$tournament->save()) {
            return $this->error(__('errors.cant_update_tournament'));
        }

        return $this->success($tournament);
    }

    /**
     * @param TournamentTypeRepository $repository
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/tournaments/types",
     *      tags={"Tournament"},
     *      summary="Get tournament types",
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
     *                      @OA\Items(ref="#/components/schemas/TournamentType")
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
    public function getTypes(TournamentTypeRepository $repository): JsonResponse
    {
        return $this->success($repository->all());
    }
}

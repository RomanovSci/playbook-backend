<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Tournament\CreateTournamentFormRequest;
use App\Http\Requests\Tournament\StartTournamentFormRequest;
use App\Repositories\TournamentGridTypeRepository;
use App\Repositories\TournamentRepository;
use App\Repositories\TournamentTypeRepository;
use App\Services\StartTournamentService;
use App\Services\Tournament\CreateTournamentService;
use Illuminate\Http\JsonResponse;

/**
 * Class TournamentController
 * @package App\Http\Controllers\API
 */
class TournamentController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/tournament",
     *      tags={"Tournament"},
     *      summary="Get all tournaments",
     *      @OA\Parameter(
     *          name="limit",
     *          description="Limit",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer")
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Tournament")
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
    public function get(GetFormRequest $request): JsonResponse
    {
        $tournaments = TournamentRepository::get(
            $request->get('limit'),
            $request->get('offset')
        );
        return $this->success($tournaments);
    }

    /**
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/tournament/type",
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
     *                      property="success",
     *                      type="boolean"
     *                  ),
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
    public function getTypes(): JsonResponse
    {
        return $this->success(TournamentTypeRepository::all());
    }

    /**
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/tournament/grid_type",
     *      tags={"Tournament"},
     *      summary="Get tournament grid types",
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/TournamentGridType")
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
    public function getGridTypes(): JsonResponse
    {
        return $this->success(TournamentGridTypeRepository::all());
    }

    /**
     * @param CreateTournamentFormRequest $request
     * @param CreateTournamentService $createTournamentService
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/tournament",
     *      tags={"Tournament"},
     *      summary="Create tournament",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "name",
     *                      "is_private",
     *                      "tournament_type_uuid",
     *                      "tournament_grid_type_uuid"
     *                  },
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="is_private",
     *                      type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="max_participants_count",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="registration_start_time",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="tournament_type_uuid",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="tournament_grid_type_uuid",
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
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "name": {
     *                              "The name field is required."
     *                          },
     *                          "is_private": {
     *                              "The is_private field is required."
     *                          },
     *                          "tournament_type_uuid": {
     *                              "The tournament_type_uuid field is required."
     *                          },
     *                          "tournament_grid_type_uuid": {
     *                              "The tournament_grid_type_uuid field is required."
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
    public function create(
        CreateTournamentFormRequest $request,
        CreateTournamentService $createTournamentService
    ): JsonResponse {
        $result = $createTournamentService->create($request->all());

        if (!$result->getSuccess()) {
            return $this->error($result->getMessage());
        }

        return $this->created($result->getData());
    }

    /**
     * @param StartTournamentFormRequest $request
     * @param StartTournamentService $startTournamentService
     * @return JsonResponse
     */
    public function start(
        StartTournamentFormRequest $request,
        StartTournamentService $startTournamentService
    ): JsonResponse {
        $result = $startTournamentService->start(
            TournamentRepository::getByUuid($request->get('tournament_uuid'))
        );

        if (!$result->getSuccess()) {
            return $this->error($result->getMessage());
        }

        return $this->success($result->getData());
    }
}

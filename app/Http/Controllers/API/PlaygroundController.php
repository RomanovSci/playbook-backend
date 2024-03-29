<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Playground\CreatePlaygroundFormRequest;
use App\Models\Organization;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\PlaygroundRepository;
use App\Repositories\PlaygroundTypesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaygroundController
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @param PlaygroundRepository $repository
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/playgrounds",
     *      tags={"Playground"},
     *      summary="Get playgrounds",
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
     *      @OA\Parameter(
     *          name="query",
     *          description="Search string",
     *          in="query",
     *          required=false,
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
     *                      @OA\Items(ref="#/components/schemas/Playground")
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
     *                          "limit": {
     *                              "The limit field is required."
     *                          },
     *                          "offset": {
     *                              "The offset field is required."
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
     *      security={{"Bearer":{}}}
     * )
     */
    public function all(GetFormRequest $request, PlaygroundRepository $repository): JsonResponse
    {
        return $this->success($repository->get($request->all()));
    }

    /**
     * @param Playground $playground
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/playgrounds/{uuid}",
     *      tags={"Playground"},
     *      summary="Get playground",
     *     @OA\Parameter(
     *          name="uuid",
     *          description="playground uuid",
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
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/Playground"
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
    public function get(Playground $playground): JsonResponse
    {
        return $this->success($playground->toArray());
    }

    /**
     * @param CreatePlaygroundFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/playgrounds",
     *      tags={"Playground"},
     *      summary="Create new playground for organization",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "name",
     *                      "description",
     *                      "address",
     *                      "opening_time",
     *                      "closing_time",
     *                  },
     *                  example={
     *                      "name": "Playground name",
     *                      "description": "Playground description",
     *                      "address": "Playground address",
     *                      "opening_time": "09:00:00",
     *                      "closing_time": "23:20:00",
     *                      "type_uuid": "0000000-1111-2222-3333-444444444444",
     *                      "organization_uuid": "0000000-1111-2222-3333-444444444444"
     *                  },
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="opening_time",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="closing_time",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="type_uuid",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="organization_uuid",
     *                      type="string"
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
     *                      ref="#/components/schemas/Playground"
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
     *                          "name": {
     *                              "The name field is required."
     *                          },
     *                          "description": {
     *                              "The description field is required."
     *                          },
     *                          "address": {
     *                              "The address field is required."
     *                          },
     *                          "opening_time": {
     *                              "The opening time field is required."
     *                          },
     *                          "closing_time": {
     *                              "The closing time field is required."
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
    public function create(CreatePlaygroundFormRequest $request): JsonResponse
    {
        /**
         * @var User $user
         * @var Organization $organization
         */
        $user = Auth::user();
        $organization = Organization::find($request->post('organization_uuid'));

        if ($organization && $user->cant('createPlayground', $organization)) {
            throw new ForbiddenHttpException(__('errors.cant_create_playground'));
        }

        /**
         * @var Playground $playground
         */
        $playground = Playground::create(array_merge($request->all(), [
            'organization_uuid' => $organization->uuid ?? null,
            'creator_uuid' => $user->uuid,
        ]));

        return $this->created($playground);
    }

    /**
     * @param PlaygroundTypesRepository $repository
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/playgrounds/types",
     *      tags={"Playground"},
     *      summary="Get all playground types",
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
     *                      @OA\Items(ref="#/components/schemas/PlaygroundType")
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
    public function getTypes(PlaygroundTypesRepository $repository): JsonResponse
    {
        return $this->success($repository->all());
    }
}

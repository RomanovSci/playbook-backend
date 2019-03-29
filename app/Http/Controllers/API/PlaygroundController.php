<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Common\SearchFormRequest;
use App\Http\Requests\Playground\PlaygroundCreateFormRequest;
use App\Models\Organization;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\PlaygroundRepository;
use App\Repositories\PlaygroundTypesRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaygroundController
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/playground",
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
     *                      "success": false,
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
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function get(GetFormRequest $request)
    {
        $playgrounds = PlaygroundRepository::get(
            $request->get('limit'),
            $request->get('offset')
        );
        return $this->success($playgrounds);
    }

    /**
     * @param SearchFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/playground/search",
     *      tags={"Playground"},
     *      summary="Search by playground name",
     *      @OA\Parameter(
     *          name="query",
     *          description="Search string",
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
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "query": {
     *                              "The query field is required."
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
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function search(SearchFormRequest $request)
    {
        $playgrounds = PlaygroundRepository::search($request->get('query'));
        return $this->success($playgrounds);
    }

    /**
     * @param PlaygroundCreateFormRequest $request
     * @return string
     *
     * @OA\Post(
     *      path="/api/playground/create",
     *      tags={"Playground"},
     *      summary="Create new playground for organization",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "name": "Playground name",
     *                      "description": "Playground description",
     *                      "address": "Playground address",
     *                      "opening_time": "09:00:00",
     *                      "closing_time": "23:20:00",
     *                      "type_uuid": "Playground type uuid",
     *                      "organization_uuid": "Organization uuid"
     *                  }
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
     *                      "success": false,
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
    public function create(PlaygroundCreateFormRequest $request)
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

        return $this->success($playground->toArray());
    }

    /**
     * @return string
     *
     * @OA\Get(
     *      path="/api/playground/types",
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
     *                      "success": false,
     *                      "message": "Unauthorized"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function getTypes()
    {
        return $this->success(PlaygroundTypesRepository::all());
    }
}

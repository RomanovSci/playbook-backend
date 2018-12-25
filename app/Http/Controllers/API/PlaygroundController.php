<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\PlaygroundCreateFormRequest;
use App\Http\Requests\Playground\PlaygroundSearchFormRequest;
use App\Models\Organization;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\PlaygroundRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaygroundController
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * @param PlaygroundSearchFormRequest $request
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
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Playground")
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
    public function search(PlaygroundSearchFormRequest $request)
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
     *                      "opening_time": "Playground opening time. Example: 09:00:00",
     *                      "closing_time": "Playground closing time. Example: 23:20:00",
     *                      "type_id": "Playground type id. Ref to PlaygroundType entity. Example: 1",
     *                      "organization_id": "Organization id"
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
     *                      ref="#/components/schemas/Playground"
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
    public function create(PlaygroundCreateFormRequest $request)
    {
        /**
         * @var Organization $organization
         * @var User $user
         */
        $organization = Organization::find($request->post('organization_id'));
        $user = Auth::user();

        if ($organization && $user->cant('createPlayground', $organization)) {
            throw new ForbiddenHttpException();
        }

        /**
         * @var Playground $playground
         */
        $playground = Playground::create(array_merge($request->all(), [
            'organization_id' => $organization->id ?? null,
            'creator_id' => $user->id,
        ]));

        return $this->success($playground->toArray());
    }
}

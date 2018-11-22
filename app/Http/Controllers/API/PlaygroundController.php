<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playground\PlaygroundCreateFormRequest;
use App\Models\Organization;
use App\Models\Playground;
use App\Repositories\PlaygroundRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class PlaygroundController
 *
 * @package App\Http\Controllers\API
 */
class PlaygroundController extends Controller
{
    /**
     * Create playground
     *
     * @param Organization $organization
     * @param PlaygroundCreateFormRequest $request
     * @return string
     *
     * @OA\Post(
     *      path="/api/playground/create/{organization_id}",
     *      tags={"Playground"},
     *      summary="Create new playground for organization",
     *      @OA\Parameter(
     *          name="organization_id",
     *          description="Organization id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
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
     *                      "type_id": "Playground type id. Ref to PlaygroundType entity. Example: 1"
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Playground")
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(
        Organization $organization,
        PlaygroundCreateFormRequest $request
    ) {
        if (Auth::user()->cant('createPlayground', $organization)) {
            return $this->forbidden();
        }

        /**
         * @var Playground $playground
         */
        $playground = Playground::create(array_merge(
            $request->all(),
            ['organization_id' => $organization->id]
        ));

        return $this->success($playground->toArray());
    }
}

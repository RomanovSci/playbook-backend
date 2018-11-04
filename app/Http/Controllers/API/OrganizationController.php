<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\OrganizationCreateFormRequest;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrganizationController
 *
 * @package App\Http\Controllers\API
 */
class OrganizationController extends Controller
{
    /**
     * @param OrganizationCreateFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/organization/create",
     *      tags={"Organization"},
     *      summary="Create organization",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "name": "Organization name",
     *                      "city_id": "City id. Ref to City entity. Example: 1",
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(ref="#/components/schemas/Organization")
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function create(OrganizationCreateFormRequest $request)
    {
        /**
         * @var Organization $organization
         */
        $organization = Organization::create(array_merge(
            $request->all(),
            ['owner_id' => Auth::user()->id]
        ));

        return $this->success($organization->toArray());
    }
}

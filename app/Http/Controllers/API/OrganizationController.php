<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Organization\OrganizationCreateFormRequest;
use App\Models\Organization;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrganizationController
 * @package App\Http\Controllers\API
 */
class OrganizationController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/organization",
     *      tags={"Organization"},
     *      summary="Get organizations",
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
     *                      @OA\Items(ref="#/components/schemas/Organization")
     *                  )
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function get(GetFormRequest $request)
    {
        $organizations = OrganizationRepository::get(
            $request->get('limit'),
            $request->get('offset')
        );
        return $this->success($organizations);
    }

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
     *                      "city_uuid": "City uuid",
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
     *                      ref="#/components/schemas/Organization"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function create(OrganizationCreateFormRequest $request)
    {
        /** @var Organization $organization */
        $organization = Organization::create(array_merge(
            $request->all(),
            ['owner_uuid' => Auth::user()->uuid]
        ));

        return $this->success($organization->toArray());
    }
}

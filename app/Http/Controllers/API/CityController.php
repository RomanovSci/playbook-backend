<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Common\SearchFormRequest;
use App\Repositories\CityRepository;

/**
 * Class CityController
 * @package App\Http\Controllers\API
 */
class CityController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/city",
     *      tags={"City"},
     *      summary="Get cities",
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
     *                      @OA\Items(ref="#/components/schemas/City")
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
    public function get(GetFormRequest $request)
    {
        $cities = CityRepository::get(
            $request->get('limit'),
            $request->get('offset')
        );
        return $this->success($cities);
    }

    /**
     * @param SearchFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/city/search",
     *      tags={"City"},
     *      summary="Search cities",
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
     *                      @OA\Items(ref="#/components/schemas/City")
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
    public function search(SearchFormRequest $request)
    {
        $cities = CityRepository::search($request->get('query'));
        return $this->success($cities);
    }
}

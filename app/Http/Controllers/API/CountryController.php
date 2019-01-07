<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\Common\SearchFormRequest;
use App\Repositories\CountryRepository;

/**
 * Class CountryController
 * @package App\Http\Controllers\API
 */
class CountryController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/api/country",
     *      tags={"Country"},
     *      summary="Get countries",
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
     *                      @OA\Items(ref="#/components/schemas/Country")
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
        $cities = CountryRepository::get(
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
     *      path="/api/country/search",
     *      tags={"Country"},
     *      summary="Search countries",
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
     *                      @OA\Items(ref="#/components/schemas/Country")
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
        $cities = CountryRepository::search($request->get('query'));
        return $this->success($cities);
    }
}

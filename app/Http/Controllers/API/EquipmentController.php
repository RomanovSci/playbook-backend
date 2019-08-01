<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\CreateEquipmentFormRequest;
use App\Models\Playground;
use App\Models\User;
use App\Repositories\EquipmentRepository;
use App\Services\Equipment\EquipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class EquipmentController
 * @package App\Http\Controllers\API
 */
class EquipmentController extends Controller
{
    /**
     * @param EquipmentRepository $repository
     * @param string $bookableType
     * @param string $uuid
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/equipments/{bookable_type}/{bookable_uuid}",
     *      tags={"Equipment"},
     *      summary="Get all equipments for bookable",
     *      @OA\Parameter(
     *          name="bookable_type",
     *          description="Trainer or playground",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="bookable_uuid",
     *          description="Bookable uuid",
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
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="array",
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Equipment"),
     *                  ),
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
     *      )
     * )
     */
    public function get(EquipmentRepository $repository, string $bookableType, string $uuid): JsonResponse
    {
        if ($bookableType === Playground::class) {
            /**
             * TODO: Implement getting equipments for playground
             */
            return $this->success();
        }

        return $this->success($repository->getByCreatorUuid($uuid));
    }

    /**
     * @param CreateEquipmentFormRequest $request
     * @param EquipmentService $equipmentService
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/equipments",
     *      tags={"Equipment"},
     *      summary="Create equipment",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "name",
     *                      "price_per_hour",
     *                      "currency",
     *                      "availability",
     *                  },
     *                  example={
     *                      "name": "Tennis racket",
     *                      "price_per_hour": "2000",
     *                      "currency": "USD",
     *                      "availability": "1",
     *                  },
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="price_per_hour",
     *                      type="integer"
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="availability",
     *                      type="integer"
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
     *                      ref="#/components/schemas/Equipment"
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
     *                          "price_per_hour": {
     *                              "The price per hour field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
     *                          },
     *                          "availability": {
     *                              "The availability field is required."
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
    public function create(
        CreateEquipmentFormRequest $request,
        EquipmentService $equipmentService
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();
        $result = $equipmentService->create($user, $request->all());

        if (!$result->getSuccess()) {
            return $this->error($result->getMessage());
        }

        return $this->created($result->getData());
    }
}

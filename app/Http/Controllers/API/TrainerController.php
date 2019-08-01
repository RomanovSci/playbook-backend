<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\TrainerInfo\CreateTrainerInfoFormRequest;
use App\Http\Requests\TrainerInfo\EditTrainerInfoFormRequest;
use App\Models\TrainerInfo;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Trainer\TrainerInfoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class TrainerController
 * @package App\Http\Controllers\API
 */
class TrainerController extends Controller
{
    /**
     * @param GetFormRequest $request
     * @param UserRepository $repository
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/trainers",
     *      tags={"Trainer"},
     *      summary="Get trainers list",
     *      @OA\Parameter(
     *          name="limit",
     *          description="Limit",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="query",
     *          required=true,
     *          @OA\Schema(type="integer"),
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
     *                      allOf={
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="total_count",
     *                                  type="integer",
     *                              ),
     *                          ),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="list",
     *                                  type="array",
     *                                  @OA\Items(
     *                                      allOf={
     *                                          @OA\Schema(ref="#/components/schemas/User"),
     *                                          @OA\Schema(
     *                                              @OA\Property(
     *                                                  property="trainer_info",
     *                                                  type="Object",
     *                                                  ref="#/components/schemas/TrainerInfo"
     *                                              ),
     *                                          ),
     *                                      }
     *                                  )
     *                              ),
     *                          ),
     *                      }
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
     *                              "The limit field is required."
     *                          },
     *                      }
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function get(GetFormRequest $request, UserRepository $repository): JsonResponse
    {
        $trainers = $repository->getByRole(
            User::ROLE_TRAINER,
            (int) $request->get('limit'),
            (int) $request->get('offset')
        );

        return $this->success([
            'total_count' => count($trainers),
            'list' => $trainers,
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/trainers/{trainer_uuid}/info",
     *      tags={"Trainer"},
     *      summary="Get trainer information",
     *      @OA\Parameter(
     *          name="trainer_uuid",
     *          description="Trainer uuid",
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
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/User"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="trainer_info",
     *                                  type="Object",
     *                                  ref="#/components/schemas/TrainerInfo"
     *                              ),
     *                          ),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="playgrounds",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/Playground")
     *                              ),
     *                          ),
     *                      }
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request"
     *      )
     * )
     */
    public function getInfo(User $user): JsonResponse
    {
        return $this->success(array_merge($user->toArray(), [
            'playgrounds' => $user->playgrounds,
            'trainer_info' => $user->trainerInfo
        ]));
    }

    /**
     * @param CreateTrainerInfoFormRequest $request
     * @param TrainerInfoService $trainerInfoService
     * @return JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/trainers/info",
     *      tags={"Trainer"},
     *      summary="Create trainer information",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "playgrounds",
     *                      "min_price",
     *                      "max_price",
     *                      "currency",
     *                  },
     *                  example={
     *                      "playgrounds": {"0000000-1111-2222-3333-444444444444"},
     *                      "about": "Short information about trainer",
     *                      "min_price": "7000",
     *                      "max_price": "9000",
     *                      "currency": "USD",
     *                      "image": "Trainer image"
     *                  },
     *                  @OA\Property(
     *                      property="playgrounds",
     *                      type="array",
     *                      @OA\Items()
     *                  ),
     *                  @OA\Property(
     *                      property="about",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="min_price",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="max_price",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="image",
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
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/TrainerInfo"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="playgrounds",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/Playground")
     *                              ),
     *                          ),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="images",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/File")
     *                              ),
     *                          ),
     *                      }
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
     *                          "playgrounds": {
     *                              "The playgrounds field is required."
     *                          },
     *                          "min_price": {
     *                              "The min price field is required."
     *                          },
     *                          "max_price": {
     *                              "The max price field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
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
    public function createInfo(
        CreateTrainerInfoFormRequest $request,
        TrainerInfoService $trainerInfoService
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        if ($user->trainerInfo) {
            return $this->error(__('errors.trainer_info_exists'));
        }

        $createResult = $trainerInfoService->create($user, $request->all());

        return $this->created(array_merge($createResult->getData('info')->toArray(), [
            'playgrounds' => $user->playgrounds,
            'images' => $createResult->getData('info')->images,
        ]));
    }

    /**
     * @param EditTrainerInfoFormRequest $request
     * @param TrainerInfo $info
     * @param TrainerInfoService $trainerInfoService
     * @return JsonResponse
     * @throws \Throwable
     *
     * @OA\Put(
     *      path="/api/trainers/info/{trainer_info_uuid}",
     *      tags={"Trainer"},
     *      summary="Edit trainer information",
     *      @OA\Parameter(
     *          name="trainer_info_uuid",
     *          description="Trainer info uuid",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "playgrounds",
     *                      "min_price",
     *                      "max_price",
     *                      "currency",
     *                  },
     *                  example={
     *                      "playgrounds": {"0000000-1111-2222-3333-444444444444"},
     *                      "about": "Short information about trainer",
     *                      "min_price": "7000",
     *                      "max_price": "9000",
     *                      "currency": "USD",
     *                      "image": "Trainer image"
     *                  },
     *                  @OA\Property(
     *                      property="playgrounds",
     *                      type="array",
     *                      @OA\Items()
     *                  ),
     *                  @OA\Property(
     *                      property="about",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="min_price",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="max_price",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="currency",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                  ),
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
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/TrainerInfo"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="playgrounds",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/Playground")
     *                              ),
     *                          ),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="images",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/File")
     *                              ),
     *                          ),
     *                      }
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
     *                          "playgrounds": {
     *                              "The playgrounds field is required."
     *                          },
     *                          "min_price": {
     *                              "The min price field is required."
     *                          },
     *                          "max_price": {
     *                              "The max price field is required."
     *                          },
     *                          "currency": {
     *                              "The currency field is required."
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
    public function editInfo(
        EditTrainerInfoFormRequest $request,
        TrainerInfo $info,
        TrainerInfoService $trainerInfoService
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        if ($user->cant('edit', $info)) {
            throw new ForbiddenHttpException(__('errors.user_cant_edit_info'));
        }

        $editResult = $trainerInfoService->edit($user, $info, $request->all());

        return $this->success(array_merge($editResult->getData('info')->toArray(), [
            'playgrounds' => $user->playgrounds,
            'images' => $editResult->getData('info')->images,
        ]));
    }
}

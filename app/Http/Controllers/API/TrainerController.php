<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\ForbiddenHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\GetFormRequest;
use App\Http\Requests\TrainerInfo\TrainerInfoCreateFormRequest;
use App\Http\Requests\TrainerInfo\TrainerInfoEditFormRequest;
use App\Models\TrainerInfo;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\TrainerService;
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
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/trainer/list",
     *      tags={"Trainer"},
     *      summary="Get trainers list",
     *      @OA\Parameter(
     *          name="limit",
     *          description="Limit",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer"),
     *      ),
     *      @OA\Parameter(
     *          name="offset",
     *          description="Offset",
     *          in="path",
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
     *                      property="success",
     *                      type="boolean",
     *                  ),
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
     *                                          @OA\Schema(
     *                                              @OA\Property(
     *                                                  property="playgrounds",
     *                                                  type="array",
     *                                                  @OA\Items(ref="#/components/schemas/Playground")
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
     *                      "limit": {
     *                          "The limit field is required."
     *                      },
     *                      "offset": {
     *                          "The limit field is required."
     *                      },
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function getTrainers(GetFormRequest $request)
    {
        $trainers = UserRepository::getByRole(
            User::ROLE_TRAINER,
            $request->get('limit'),
            $request->get('offset')
        );

        return $this->success([
            'total_count' => UserRepository::getCountByRole(User::ROLE_TRAINER),
            'list' => $trainers,
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/api/trainer/info/{trainer_uuid}",
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
    public function getTrainerInfo(User $user)
    {
        return $this->success(array_merge($user->toArray(), [
            'playgrounds' => $user->playgrounds,
            'trainer_info' => $user->trainerInfo
        ]));
    }

    /**
     * @param TrainerInfoCreateFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/trainer/info/create",
     *      tags={"Trainer"},
     *      summary="Create trainer information",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "playgrounds": "Array of playgrounds uuids",
     *                      "about": "Short information about trainer",
     *                      "min_price": "Min price in cents. Example: 7000. (70RUB)",
     *                      "max_price": "Max price in cents.",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB",
     *                      "image": "Trainer image"
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
     *                      "playgrounds": {
     *                          "The playgrounds field is required."
     *                      },
     *                      "min_price": {
     *                          "The min price field is required."
     *                      },
     *                      "max_price": {
     *                          "The max price field is required."
     *                      },
     *                      "currency": {
     *                          "The currency field is required."
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
    public function createInfo(TrainerInfoCreateFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->trainerInfo) {
            return $this->error(__('errors.trainer_info_exists'));
        }

        $createResult = TrainerService::createInfo($user, $request->all());

        return $this->success(array_merge($createResult->getData('info')->toArray(), [
            'playgrounds' => $user->playgrounds,
            'images' => $createResult->getData('info')->images,
        ]));
    }

    /**
     * @param TrainerInfoEditFormRequest $request
     * @param TrainerInfo $info
     * @return JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/trainer/info/edit/{trainer_info_uuid}",
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
     *                  example={
     *                      "playgrounds": "Array of playgrounds uuids",
     *                      "about": "Short information about trainer",
     *                      "min_price": "Min price in cents. Example: 7000. (70RUB)",
     *                      "max_price": "Max price in cents.",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB",
     *                      "image": "Trainer image"
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
     *                      "playgrounds": {
     *                          "The playgrounds field is required."
     *                      },
     *                      "min_price": {
     *                          "The min price field is required."
     *                      },
     *                      "max_price": {
     *                          "The max price field is required."
     *                      },
     *                      "currency": {
     *                          "The currency field is required."
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
    public function editInfo(TrainerInfoEditFormRequest $request, TrainerInfo $info)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->cant('edit', $info)) {
            throw new ForbiddenHttpException(__('errors.user_cant_edit_info'));
        }

        $editResult = TrainerService::editInfo($user, $info, $request->all());
        return $this->success(array_merge($editResult->getData('info')->toArray(), [
            'playgrounds' => $user->playgrounds,
            'images' => $editResult->getData('info')->images,
        ]));
    }
}

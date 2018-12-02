<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerInfo\TrainerInfoCreateFormRequest;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\UserCreateFormRequest;
use App\Http\Requests\User\VerifyPhoneFormRequest;
use App\Models\Country;
use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\API
 */
class UserController extends Controller
{
    /**
     * @param UserCreateFormRequest $request
     * @return JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/register",
     *      tags={"User"},
     *      summary="Register new user",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "first_name": "User first name.",
     *                      "last_name": "User last name.",
     *                      "phone": "User phone without '+' symbol",
     *                      "password": "User password.",
     *                      "c_password": "User password confirm.",
     *                      "is_trainer": "Boolean flag (0 or 1)"
     *                  }
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": true,
     *                      "message": "string",
     *                      "data": {
     *                          "token": "Bearer token"
     *                      }
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "phone": "The phone must be a number."
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request"
     *      )
     * )
     */
    public function register(UserCreateFormRequest $request)
    {
        /** @var Country $country */
        $fields = $request->all();
        $fields['password'] = bcrypt($fields['password']);
        $fields['verification_code'] = rand(100000, 999999);

        DB::beginTransaction();
        try {
            /** @var User $user */
            $user = User::create($fields);
            $user->assignRole($fields['is_trainer'] ? User::ROLE_TRAINER : User::ROLE_USER);
            $token = $user->createToken('MyApp');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success([
            'access_token' => $token->accessToken,
        ]);
    }

    /**
     * @param LoginFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/login",
     *      tags={"User"},
     *      summary="Login user",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "phone": "User phone without '+' symbol",
     *                      "password": "User password.",
     *                  }
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": true,
     *                      "message": "string",
     *                      "data": {
     *                          "access_token": "Bearer token",
     *                          "roles": {
     *                              "user",
     *                              "trainer",
     *                              "organization-admin",
     *                              "admin"
     *                          }
     *                      }
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "phone": "The phone must be a number."
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function login(LoginFormRequest $request)
    {
        /** @var User $user */
        $phone = $request->get('phone');
        $password = $request->get('password');
        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->unauthorized();
        }
        $token = $user->createToken('MyApp');

        return $this->success([
            'access_token' => $token->accessToken,
            'roles' => $user->getRoleNames(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"User"},
     *      summary="Logout user",
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": true,
     *                      "message": "string"
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid parameters",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthenticated"
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->success();
    }

    /**
     * @param VerifyPhoneFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/phone-verify",
     *      tags={"User"},
     *      summary="Verify user phone",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "code": "Verification code. Example: 001122"
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": "true | false",
     *                      "message": "Success | Incorrect verification code"
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid parameters",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthenticated"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "code": {
     *                          "The code must be 6 digits."
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function verifyPhone(VerifyPhoneFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->verification_code !== $request->post('code')) {
            return $this->error(200, [], 'Incorrect verification code');
        }

        $user->phone_verified_at = Carbon::now();
        $user->save();

        return $this->success();
    }

    /**
     * @param TrainerInfoCreateFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     *
     * @OA\Post(
     *      path="/api/trainer/info-create",
     *      tags={"User"},
     *      summary="Create trainer information",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "playgrounds": "Array of playgrounds ids. Example: [1,2,3]",
     *                      "about": "Short information about trainer",
     *                      "min_price": "Min price in cents. Example: 7000. (70RUB)",
     *                      "max_price": "Max price in cents.",
     *                      "currency": "Currency: RUB, UAH, USD, etc. Default: RUB"
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
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/TrainerInfo"),
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
     *         )
     *      ),
     *      @OA\Response(
     *          response="422",
     *          description="Invalid parameters"
     *      ),
     *      security={{"Bearer":{}}}
     * )
     */
    public function createTrainerInfo(TrainerInfoCreateFormRequest $request)
    {
        /**
         * @var User $user
         * @var TrainerInfo $trainerInfo
         */
        $user = Auth::user();
        $playgroundIds = $request->post('playgrounds');

        DB::beginTransaction();

        try {
            $trainerInfo = TrainerInfo::create(array_merge($request->all(), [
                'user_id' => $user->id,
            ]));

            foreach ($playgroundIds as $playgroundId) {
                UserPlayground::create([
                    'user_id' => $user->id,
                    'playground_id' => $playgroundId
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success(array_merge($trainerInfo->toArray(), [
            'playgrounds' => $user->playgrounds()->get(),
        ]));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\UserCreateFormRequest;
use App\Models\Country;
use App\Models\User;
use App\Repositories\CountryRepository;
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
     * @throws \Exception
     *
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Passport"},
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
     *                      "message": "Success",
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

        DB::beginTransaction();
        try {
            /** @var User $user */
            $user = User::create($fields);
            $user->assignRole($fields['is_trainer'] ? User::ROLE_TRAINER : User::ROLE_USER);
            $token = $user->createToken('MyApp');

            DB::commit();
        } catch (\Exception $e) {
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
     *      tags={"Passport"},
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
     *                      "message": "Success",
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
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Passport"},
     *      summary="Logout user",
     *      @OA\Response(
     *          response="200",
     *          description="Ok",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": true,
     *                      "message": "Success"
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
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateFormRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
     *                      "phone": "User phone with country code, without plus symbol.",
     *                      "password": "User password.",
     *                      "c_password": "User password confirm."
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
     * ),
     * @OA\Post(
     *      path="/oauth/token",
     *      tags={"Passport"},
     *      summary="Get access and refresh tokens",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "grant_type": "Grant type. 'password' always.",
     *                      "client_id": "Client id",
     *                      "client_secret": "Client secret",
     *                      "username": "User phone with country code without plus symbol.",
     *                      "password": "User password."
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
     *                      "token_type": "Bearer",
     *                      "expires_in": "1296000",
     *                      "access_token": "access_token",
     *                      "refresh_token": "refresh_token"
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Invalid client",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "error": "invalid_client",
     *                      "message": "Client authentication failed"
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "error": "invalid_request",
     *                      "message": "The request is missing a required parameter ..."
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function register(UserCreateFormRequest $request)
    {
        $fields = $request->all();
        $fields['password'] = bcrypt($fields['password']);
        DB::beginTransaction();

        try {
            /** @var User $user */
            $user = User::create($fields);
            $user->assignRole(User::ROLE_USER);
            $token = $user->createToken('MyApp');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success([
            'access_token' => $token->accessToken,
        ]);
    }
}

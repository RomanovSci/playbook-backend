<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateFormRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
     *                      "first_name": "Roman",
     *                      "last_name": "Bylbas",
     *                      "phone": "380501234567",
     *                      "password": "iampassword",
     *                      "c_password": "iampassword"
     *                  }
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful registration",
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
     *          description="Invalid parameters"
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
     *                      "grant_type": "password",
     *                      "client_id": "2",
     *                      "client_secret": "megasecretclientsecret",
     *                      "username": "0507707018",
     *                      "password": "1488"
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
     *          ),
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Invalid request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "error": "invalid_request",
     *                      "message": "The request is missing a required parameter, includes an invalid parameter value, includes a parameter more than once, or is otherwise malformed."
     *                  },
     *              )
     *          ),
     *      )
     * )
     */
    public function register(UserCreateFormRequest $request)
    {
        $fields = $request->all();
        $fields['password'] = bcrypt($fields['password']);

        /** @var User $user */
        $user = User::create($fields);
        $user->assignRole(User::ROLE_USER);
        $token = $user->createToken('MyApp');

        return $this->success([
            'access_token' => $token->accessToken,
        ]);
    }
}

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
     * )
     */
    public function register(UserCreateFormRequest $request)
    {
        $fields = $request->all();
        $fields['password'] = bcrypt($fields['password']);

        /**
         * @var User $user
         */
        $user = User::create($fields);
        $user->assignRole(User::ROLE_USER);

        return $this->success([
            'token' => $user->createToken('MyApp')->accessToken,
        ]);
    }
}

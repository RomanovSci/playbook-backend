<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Create as UserCreateRequest;
use App\Http\Requests\User\Create;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\API
 * @resource User
 */
class UserController extends Controller
{
    /**
     * Register new user
     *
     * @response {
     *      "success": true,
     *      "token": "Access token"
     * }
     *
     * @param UserCreateRequest $request
     * @return Response
     */
    public function register(UserCreateRequest $request)
    {
        $fields = $request->all();
        $fields['password'] = bcrypt($fields['password']);

        /**
         * @var User $user
         */
        $user = User::create($fields);
        $user->assignRole(User::ROLE_USER);

        return response()->json([
            'success' => true,
            'token' => $user->createToken('MyApp')->accessToken,
        ]);
    }
}

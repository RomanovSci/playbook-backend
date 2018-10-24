<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

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
     * @param CreateUser $request
     * @return Response
     */
    public function register(CreateUser $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        /**
         * @var User $user
         */
        $user = User::create($input);
        $user->assignRole(User::ROLE_USER);

        return response()->json([
            'success' => true,
            'token' => $user->createToken('MyApp')->accessToken,
        ]);
    }
}

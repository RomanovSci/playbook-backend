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
        $user = new User();
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->phone = $request->get('phone');
        $user->password = bcrypt($request->get('password'));

        $user->save();
        $user->assignRole(User::ROLE_USER);

        return response()->json([
            'success' => true,
            'token' => $user->createToken('MyApp')->accessToken,
        ]);
    }
}

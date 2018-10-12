<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
     *      "data": {
     *          "token": "Access token",
     *          "email": "User email"
     *      }
     * }
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'=>$validator->errors()
            ], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        /**
         * @var User $user
         */
        $user = User::create($input);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $user->createToken('MyApp')->accessToken,
                'email' => $user->email,
            ]
        ]);
    }
}

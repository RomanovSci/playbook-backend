<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Http\UnauthorizedHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\RegisterFormRequest;
use App\Http\Requests\User\ResendVerificationCodeFormRequest;
use App\Http\Requests\User\VerifyPhoneFormRequest;
use App\Models\Country;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\SmsDeliveryService\SmsDeliveryServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserController extends Controller
{
    /**
     * @var SmsDeliveryServiceInterface
     */
    protected $smsDeliveryService;

    /**
     * UserController constructor.
     *
     * @param SmsDeliveryServiceInterface $smsDeliveryService
     */
    public function __construct(SmsDeliveryServiceInterface $smsDeliveryService)
    {
        $this->smsDeliveryService = $smsDeliveryService;
    }

    /**
     * @param RegisterFormRequest $request
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
     *          response="400",
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
    public function register(RegisterFormRequest $request)
    {
        /** @var Country $country */
        $fields = $request->all();
        $fields['verification_code'] = rand(100000, 999999);
        $fields['password'] = bcrypt($fields['password'] ?? $fields['verification_code']);

        DB::beginTransaction();
        try {
            /**
             * @var User $user
             * @var PersonalAccessTokenResult $token
             */
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
            'verification_code' => $fields['verification_code'], //TODO: Remove
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
     *                          },
     *                          "id": 1,
     *                          "first_name": "Play",
     *                          "last_name": "Book",
     *                          "phone": 911,
     *                          "phone_verified_at": "2001-01-01 00:00:00"
     *                      }
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
            throw new UnauthorizedHttpException();
        }
        $token = $user->createToken('MyApp');

        return $this->success(array_merge([
            'access_token' => $token->accessToken,
            'roles' => $user->getRoleNames(),
        ], $user->toArray()));
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
     *      path="/api/phone_verify",
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
     * @param ResendVerificationCodeFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/resend_verification_code",
     *      tags={"User"},
     *      summary="Resend verification code to phone number",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "phone": "Requested phone number. Example: 0501234567"
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
     *                      "success": "true",
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
    public function resendVerificationCode(ResendVerificationCodeFormRequest $request)
    {
        /** @var User $user */
        $user = UserRepository::getByPhone($request->get('phone'));
        return $this->success([
            'verification_code' => $user->verification_code,
        ]);
    }
}

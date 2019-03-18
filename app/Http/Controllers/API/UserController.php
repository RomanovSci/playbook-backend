<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\RegisterFormRequest;
use App\Http\Requests\User\ResendVerificationCodeFormRequest;
use App\Http\Requests\User\ResetPasswordFormRequest;
use App\Http\Requests\User\VerifyPhoneFormRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserController extends Controller
{
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
     *                      "middle_name": "User middle name",
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
     *                          "access_token": "Bearer token",
     *                          "verification_code": "Phone verification code. Empty for prod env",
     *                          "roles": {
     *                              "user",
     *                              "trainer",
     *                              "organization-admin",
     *                              "admin"
     *                          },
     *                          "uuid": "User uuid",
     *                          "phone": "911",
     *                          "first_name": "Play",
     *                          "last_name": "Book",
     *                          "timezone_uuid": "Timezone uuid",
     *                          "updated_at": "2000-00-00 00:00:00",
     *                          "created_at": "2000-00-00 00:00:00"
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
        return $this->success(UserService::register($request->all())->getData());
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
     *                          "uuid": "User uuid",
     *                          "first_name": "Play",
     *                          "last_name": "Book",
     *                          "middle_name": "Middle",
     *                          "timezone_uuid": "Timezone uuid",
     *                          "language_code": "UA",
     *                          "city_uuid": "City uuid",
     *                          "phone": 911,
     *                          "phone_verified_at": "2000-00-00 00:00:00",
     *                          "updated_at": "2000-00-00 00:00:00",
     *                          "created_at": "2000-00-00 00:00:00"
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
        return $this->success(UserService::login($request->all())->getData());
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
     *          response="401",
     *          description="Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Unauthorized"
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
    public function verifyPhone(VerifyPhoneFormRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->verification_code !== $request->post('code')) {
            return $this->error(400, [], __('errors.incorrect_verification_code'));
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
     *                      "phone": "Phone number. Example: 0501234567"
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
     *                      "phone": {
     *                          "The phone must be a number."
     *                      }
     *                  },
     *              )
     *          )
     *      )
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

    /**
     * @param ResetPasswordFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/reset_password",
     *      tags={"User"},
     *      summary="Reset password request",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "phone": "Phone number. Example: 0501234567"
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
     *                      "phone": {
     *                          "The phone must be a number."
     *                      }
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function resetPassword(ResetPasswordFormRequest $request)
    {
        $resetResult = UserService::resetPassword(
            UserRepository::getByPhone($request->get('phone'))
        );

        return $resetResult->getSuccess()
            ? $this->success($resetResult->getData('passwordReset'))
            : $this->error(400, [], $resetResult->getMessage());
    }
}

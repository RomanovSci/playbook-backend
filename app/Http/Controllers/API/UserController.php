<?php
declare(strict_types = 1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginFormRequest;
use App\Http\Requests\User\RegisterFormRequest;
use App\Http\Requests\User\ResendVerificationCodeFormRequest;
use App\Http\Requests\User\ResetPasswordFormRequest;
use App\Http\Requests\User\VerifyPhoneFormRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\SmsDelivery\SmsDeliveryService;
use App\Services\User\LoginService;
use App\Services\User\RegisterService;
use App\Services\User\ResetPasswordService;
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
     * @param RegisterService $registerService
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
     *                  type="object",
     *                  required={
     *                      "first_name",
     *                      "last_name",
     *                      "phone",
     *                      "is_trainer"
     *                  },
     *                  example={
     *                      "first_name": "First name",
     *                      "last_name": "Last name",
     *                      "middle_name": "Middle name",
     *                      "phone": "123456789",
     *                      "password": "1111",
     *                      "c_password": "1111",
     *                      "is_trainer": "1",
     *                  },
     *                  @OA\Property(
     *                      property="first_name",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="last_name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="middle_name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="c_password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="is_trainer",
     *                      type="boolean"
     *                  ),
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
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
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "first_name": {
     *                              "The first name field is required."
     *                          },
     *                          "last_name": {
     *                              "The last name field is required."
     *                          },
     *                          "phone": {
     *                              "The phone field is required."
     *                          },
     *                          "is_trainer": {
     *                              "The trainer field is required."
     *                          },
     *                          "password": {
     *                              "The password field is required."
     *                          },
     *                          "c_password": {
     *                              "The c_password field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     * )
     */
    public function register(RegisterFormRequest $request, RegisterService $registerService): JsonResponse
    {
        return $this->success($registerService->register($request->all())->getData());
    }

    /**
     * @param LoginFormRequest $request
     * @param LoginService $loginService
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
     *                  type="object",
     *                  required={
     *                      "phone",
     *                      "password"
     *                  },
     *                  example={
     *                      "phone": "1234567890",
     *                      "password": "1111"
     *                  },
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                  ),
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
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
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "phone": {
     *                              "The phone field is required."
     *                          },
     *                          "password": {
     *                              "The password field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Forbidden"
     *                  },
     *              )
     *          )
     *      ),
     * )
     */
    public function login(LoginFormRequest $request, LoginService $loginService): JsonResponse
    {
        return $this->success($loginService->login($request->all())->getData());
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
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": true,
     *                      "message": "string",
     *                      "data": {}
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
    public function logout(Request $request): JsonResponse
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
     *                  type="object",
     *                  required={
     *                      "code"
     *                  },
     *                  example={
     *                      "code": "AbC123"
     *                  },
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                  ),
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": "true",
     *                      "message": "Success",
     *                      "data": {}
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "code": {
     *                              "The code field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
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
    public function verifyPhone(VerifyPhoneFormRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->verification_code !== $request->post('code')) {
            return $this->error(__('errors.incorrect_verification_code'));
        }

        $user->phone_verified_at = Carbon::now();
        $user->save();

        return $this->success();
    }

    /**
     * @param ResendVerificationCodeFormRequest $request
     * @param SmsDeliveryService $smsDeliveryService
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
     *                  type="object",
     *                  required={
     *                      "phone"
     *                  },
     *                  example={
     *                      "phone": "1234567890"
     *                  },
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                  ),
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": "true",
     *                      "message": "Success",
     *                      "data": {}
     *                  }
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "phone": {
     *                              "The phone field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function resendVerificationCode(
        ResendVerificationCodeFormRequest $request,
        SmsDeliveryService $smsDeliveryService
    ): JsonResponse {
        /** @var User $user */
        $user = UserRepository::getByPhone($request->get('phone'));
        $smsDeliveryService->send($user->phone, $user->verification_code);

        return $this->success(
            app()->environment() === 'production'
                ? []
                : ['verification_code' => $user->verification_code]
        );
    }

    /**
     * @param ResetPasswordFormRequest $request
     * @param ResetPasswordService $resetPasswordService
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
     *                  type="object",
     *                  required={
     *                      "phone"
     *                  },
     *                  example={
     *                      "phone": "1234567890"
     *                  },
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                  ),
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="success",
     *                      type="boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      ref="#/components/schemas/PasswordReset"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  example={
     *                      "success": false,
     *                      "message": "Validation error",
     *                      "data": {
     *                          "phone": {
     *                              "The phone field is required."
     *                          }
     *                      }
     *                  },
     *              )
     *          )
     *      )
     * )
     */
    public function resetPassword(
        ResetPasswordFormRequest $request,
        ResetPasswordService $resetPasswordService
    ): JsonResponse {
        $resetResult = $resetPasswordService->reset(UserRepository::getByPhone($request->get('phone')));

        return $resetResult->getSuccess()
            ? $this->success($resetResult->getData('passwordReset'))
            : $this->error($resetResult->getMessage());
    }
}

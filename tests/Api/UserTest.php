<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class UserTest
 * @package Tests\Api
 */
class UserTest extends ApiTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $authorizationHeader;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => bcrypt('1111'),
            'status' => User::STATUS_INACTIVE,
            'verification_code' => '111111',
        ]);
        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken
        ];
    }

    /**
     * @return void
     */
    public function testRegisterUserSuccess(): void
    {
        $data = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'phone' => $this->faker->randomNumber(9),
            'password' => '123456789',
            'c_password' => '123456789',
            'is_trainer' => true,
        ];

        $this->post(route('user.register'), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'roles' => [User::ROLE_TRAINER],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'middle_name' => $data['middle_name'],
                'phone' => $data['phone'],
                'status' => User::STATUS_ACTIVE,
            ]));
    }

    /**
     * @return void
     */
    public function testRegisterUserValidationError(): void
    {
        $this->post(route('user.register'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'first_name' => [],
                'last_name' => [],
                'phone' => [],
                'is_trainer' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testLoginUserSuccess(): void
    {
        $this->post(route('user.login'), ['phone' => $this->user->phone, 'password' => '1111'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'roles' => [],
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'middle_name' => $this->user->middle_name,
                'phone' => $this->user->phone,
                'status' => $this->user->status,
                'timezone_uuid' => null,
                'language_code' => null,
                'city_uuid' => null,
                'phone_verified_at' => null,
                'created_at' => $this->user->created_at,
                'updated_at' => $this->user->updated_at,
            ]));
    }

    /**
     * @return void
     */
    public function testLoginUserBadRequest(): void
    {
        $this->post(route('user.login'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'phone' => [],
                'password' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testLogoutUserSuccess(): void
    {
        $this->post(route('user.logout'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse());
    }

    /**
     * @return void
     */
    public function testLogoutUserUnauthorized(): void
    {
        $this->post(route('user.logout'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testPhoneVerifySuccess(): void
    {
        $this->post(route('user.verify_phone'), ['code' => $this->user->verification_code], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse());
    }

    /**
     * @return void
     */
    public function testPhoneVerifyUnauthorized(): void
    {
        $this->post(route('user.verify_phone'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testPhoneVerifyValidationError(): void
    {
        $this->post(route('user.verify_phone'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse(['code' => []]));
    }

    /**
     * @return void
     */
    public function testResendVerificationCodeSuccess(): void
    {
        $this->post(route('user.resend_verification_code'), ['phone' => $this->user->phone])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'verification_code' => $this->user->verification_code,
            ]));
    }

    /**
     * @return void
     */
    public function testResendVerificationCodeValidationError(): void
    {
        $this->post(route('user.resend_verification_code'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse(['phone' => []]));
    }

    /**
     * @return void
     */
    public function testResetPasswordSuccess(): void
    {
        $response = $this->post(route('user.reset_password'), ['phone' => $this->user->phone]);
        $content = json_decode($response->getContent());
        $response->assertStatus(Response::HTTP_OK)
            ->assertExactJson($this->successResponse([
                'uuid' => $content->data->uuid,
                'user_uuid' => $this->user->uuid,
                'reset_code' => $content->data->reset_code,
                'expired_at' => $content->data->expired_at,
                'created_at' => $content->data->created_at,
                'updated_at' => $content->data->updated_at,
            ]));
    }

    /**
     * @return void
     */
    public function testResetPasswordValidationError(): void
    {
        $this->post(route('user.reset_password'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'phone' => []
            ]));
    }
}

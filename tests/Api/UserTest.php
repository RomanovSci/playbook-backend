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
            ->assertJson([
                'success' => true,
                'message' => 'Success',
                'data' => [
                    'roles' => [User::ROLE_TRAINER],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'],
                    'phone' => $data['phone'],
                    'status' => User::STATUS_INACTIVE,
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testRegisterUserValidationError(): void
    {
        $this->post(route('user.register'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
                'data' => [
                    'first_name' => [],
                    'last_name' => [],
                    'phone' => [],
                    'is_trainer' => [],
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testLoginUserSuccess(): void
    {
        /** @var User $user */
        $password = '1111';
        $user = factory(User::class)->create([
            'password' => bcrypt($password),
            'status' => User::STATUS_INACTIVE,
        ]);

        $this->post(route('user.login'), ['phone' => $user->phone, 'password' => $password])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Success',
                'data' => [
                    'roles' => [],
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'middle_name' => $user->middle_name,
                    'phone' => $user->phone,
                    'status' => $user->status,
                    'timezone_uuid' => null,
                    'language_code' => null,
                    'city_uuid' => null,
                    'phone_verified_at' => null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testLoginUserBadRequest(): void
    {
        $this->post(route('user.login'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
                'data' => [
                    'phone' => [],
                    'password' => [],
                ]
            ]);
    }
}

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
}

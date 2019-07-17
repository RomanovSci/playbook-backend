<?php
declare(strict_types = 1);

namespace Tests\Api;

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
            'is_trainer' => 1,
        ];
        $response = $this->call('POST', route('user.register'), $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => true,
            'message' => 'Success',
            'data' => [
            ]
        ]);
    }
}

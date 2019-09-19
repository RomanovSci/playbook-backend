<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class CityTest
 * @package Tests\Api
 */
class CityTest extends ApiTestCase
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
            'status' => User::STATUS_ACTIVE,
            'verification_code' => '111111',
        ]);
        $this->user->assignRole(User::ROLE_TRAINER);
        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken
        ];
    }

    /**
     * @return void
     */
    public function testGetCitiesSuccess(): void
    {
        $this->get(route('city.get', ['limit' => 1, 'offset' => 0]), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse());
    }

    /**
     * @return void
     */
    public function testGetCitiesValidationError(): void
    {
        $this->get(route('city.get'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetCitiesUnauthorized(): void
    {
        $this->get(route('city.get'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

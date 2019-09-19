<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Playground;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class PlaygroundTest
 * @package Tests\Api
 */
class PlaygroundTest extends ApiTestCase
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
     * @var Playground
     */
    protected $playground;

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
        $this->playground = factory(Playground::class)->create([
            'creator_uuid' => $this->user->uuid,
            'created_at' => now(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetPlaygroundsSuccess(): void
    {
        $this->get(route('playground.get', ['limit' => 1, 'offset' => 0]), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->playground->uuid,
                    'organization_uuid' => $this->playground->organization_uuid,
                    'creator_uuid' => $this->playground->creator_uuid,
                    'name' => $this->playground->name,
                    'description' => $this->playground->description,
                    'address' => $this->playground->address,
                    'opening_time' => $this->playground->opening_time,
                    'closing_time' => $this->playground->closing_time,
                    'type_uuid' => $this->playground->type_uuid,
                    'created_at' => $this->playground->created_at->toDateTimeString(),
                    'updated_at' => $this->playground->updated_at->toDateTimeString(),
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testGetPlaygroundsValidationError(): void
    {
        $this->get(route('playground.get'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetPlaygroundsUnauthorized(): void
    {
        $this->get(route('playground.get'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

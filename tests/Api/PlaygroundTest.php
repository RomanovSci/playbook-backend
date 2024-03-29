<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Playground;
use App\Models\PlaygroundType;
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
    public function testGetAllPlaygroundsSuccess(): void
    {
        $this->get(route('playground.all', ['limit' => 1, 'offset' => 0]), $this->authorizationHeader)
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
    public function testGetAllPlaygroundsValidationError(): void
    {
        $this->get(route('playground.all'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetAllPlaygroundsUnauthorized(): void
    {
        $this->get(route('playground.all'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testCreatePlaygroundSuccess(): void
    {
        /** @var PlaygroundType $type */
        $type = factory(PlaygroundType::class)->create();
        $data = [
            'name' => 'name',
            'description' => 'description',
            'address' => 'address',
            'opening_time' => '09:00:00',
            'closing_time' => '17:00:00',
            'type_uuid' => $type->uuid->toString(),
        ];

        $this->post(route('playground.create'), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($this->createdResponse([
                'name' => $data['name'],
                'description' => $data['description'],
                'address' => $data['address'],
                'opening_time' => $data['opening_time'],
                'closing_time' => $data['closing_time'],
                'type_uuid' => $data['type_uuid'],
            ]));
    }

    /**
     * @return void
     */
    public function testCreatePlaygroundValidationError(): void
    {
        $this->post(route('playground.create'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'name' => [],
                'description' => [],
                'address' => [],
                'opening_time' => [],
                'closing_time' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testCreatePlaygroundUnauthorized(): void
    {
        $this->post(route('playground.create'), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testGetPlaygroundTypesSuccess(): void
    {
        $this->get(route('playground.get_types'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse());
    }

    /**
     * @return void
     */
    public function testGetPlaygroundTypesUnauthorized(): void
    {
        $this->get(route('playground.get_types'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

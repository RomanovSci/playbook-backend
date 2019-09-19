<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class EquipmentTest
 * @package Tests\Api
 */
class EquipmentTest extends ApiTestCase
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
     * @var Equipment
     */
    protected $equipment;

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
        $this->equipment = factory(Equipment::class)->create([
            'creator_uuid' => $this->user->uuid,
        ]);
    }

    /**
     * @return void
     */
    public function testGetEquipmentsSuccess(): void
    {
        $this->get(route('equipment.get', ['bookable_type' => 'trainer', 'bookable_uuid' => $this->user->uuid]))
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->equipment->uuid->toString(),
                    'name' => $this->equipment->name,
                    'creator_uuid' => $this->equipment->creator_uuid,
                    'price_per_hour' => $this->equipment->price_per_hour,
                    'currency' => $this->equipment->currency,
                    'availability' => $this->equipment->availability,
                    'created_at' => $this->equipment->created_at->toDateTimeString(),
                    'updated_at' => $this->equipment->updated_at->toDateTimeString(),
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testCreateEquipmentSuccess(): void
    {
        $data = [
            'name' => 'name',
            'price_per_hour' => 1,
            'currency' => 'USD',
            'availability' => 1,
        ];

        $this->post(route('equipment.create'), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($this->createdResponse(array_merge($data, [
                'creator_uuid' => $this->user->uuid,
            ])));
    }

    /**
     * @return void
     */
    public function testCreateEquipmentValidationError(): void
    {
        $this->post(route('equipment.create'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'name' => [],
                'price_per_hour' => [],
                'currency' => [],
                'availability' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testCreateEquipmentUnauthorized(): void
    {
        $this->post(route('equipment.create'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

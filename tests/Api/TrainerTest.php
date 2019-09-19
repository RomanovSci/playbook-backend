<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Playground;
use App\Models\TrainerInfo;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class TrainerTest
 * @package Tests\Api
 */
class TrainerTest extends ApiTestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Playground
     */
    protected $playground;

    /**
     * @var TrainerInfo
     */
    protected $trainerInfo;

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
        $this->playground = factory(Playground::class)->create(['creator_uuid' => $this->user->uuid]);

        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken
        ];
    }

    /**
     * @return void
     */
    public function testGetTrainerListSuccess(): void
    {
        $this->call('GET', route('trainer.get'), ['limit' => 1, 'offset' => 1])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'total_count' => 1,
                'list' => []
            ]));
    }

    /**
     * @return void
     */
    public function testGetTrainerListValidationError(): void
    {
        $this->call('GET', route('trainer.get'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'offset' => [],
                'limit' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetTrainerInfoSuccess(): void
    {
        $this->call('GET', route('trainer.get_info', ['user' => $this->user->uuid]))
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'uuid' => $this->user->uuid,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'middle_name' => $this->user->middle_name,
                'phone' => $this->user->phone,
                'status' => $this->user->status,
                'timezone_uuid' => $this->user->timezone_uuid,
                'language_code' => $this->user->language_code,
                'city_uuid' => $this->user->city_uuid,
                'phone_verified_at' => $this->user->phone_verified_at,
                'created_at' => $this->user->created_at,
                'updated_at' => $this->user->updated_at,
                'trainer_info' => null,
            ]));
    }

    /**
     * @return void
     */
    public function testCreateTrainerInfoSuccess(): void
    {
        $data = [
            'playgrounds' => [(string) $this->playground->uuid],
            'about' => 'test',
            'min_price' => 1,
            'max_price' => 2,
            'currency' => 'USD',
        ];

        $this->post(route('trainer.create_info'), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($this->createdResponse(array_merge($data, [
                'user_uuid' => $this->user->uuid,
                'playgrounds' => [],
                'images' => [],
            ])));
    }

    /**
     * @return void
     */
    public function testCreateTrainerInfoUnauthorized(): void
    {
        $this->post(route('trainer.create_info'), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testCreateTrainerInfoValidationError(): void
    {
        $this->post(route('trainer.create_info'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'playgrounds' => [],
                'currency' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testEditTrainerInfoSuccess(): void
    {
        /** @var TrainerInfo $trainerInfo */
        $trainerInfo = factory(TrainerInfo::class)->create(['user_uuid' => $this->user->uuid]);
        $data = [
            'about' => 'edit',
            'playgrounds' => [(string) $this->playground->uuid],
            'currency' => 'UAH',
        ];

        $this->put(route('trainer.edit_info', ['info' => $trainerInfo->uuid]), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'uuid' => $trainerInfo->uuid,
                'user_uuid' => $trainerInfo->user_uuid,
                'about' => $data['about'],
                'currency' => $data['currency'],
            ]));
    }

    /**
     * @return void
     */
    public function testEditTrainerInfoUnauthorized(): void
    {
        /** @var TrainerInfo $trainerInfo */
        $trainerInfo = factory(TrainerInfo::class)->create(['user_uuid' => $this->user->uuid]);
        $this->put(route('trainer.edit_info', ['info' => $trainerInfo->uuid]), [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testEditTrainerInfoValidationError(): void
    {
        /** @var TrainerInfo $trainerInfo */
        $trainerInfo = factory(TrainerInfo::class)->create(['user_uuid' => $this->user->uuid]);
        $this->put(route('trainer.edit_info', ['info' => $trainerInfo->uuid]), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'playgrounds' => [],
                'currency' => [],
            ]));
    }
}

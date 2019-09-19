<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\City;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\CityRepository;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class OrganizationTest
 * @package Tests\Api
 */
class OrganizationTest extends ApiTestCase
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
     * @var Organization
     */
    protected $organization;

    /**
     * @var City
     */
    protected $city;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->city = (new CityRepository())->get(['limit' => 1, 'offset' => 0])->last();
        $this->user = factory(User::class)->create([
            'password' => bcrypt('1111'),
            'status' => User::STATUS_ACTIVE,
            'verification_code' => '111111',
        ]);
        $this->user->assignRole(User::ROLE_ADMIN);
        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken
        ];
        $this->organization = factory(Organization::class)->create([
            'owner_uuid' => $this->user->uuid,
            'city_uuid' => $this->city->uuid,
        ]);
    }

    /**
     * @return void
     */
    public function testGetOrganizationsSuccess(): void
    {
        $this->get(route('organization.get', ['limit' => 1, 'offset' => 0]), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->organization->uuid,
                    'name' => $this->organization->name,
                    'city_uuid' => $this->organization->city_uuid,
                    'owner_uuid' => $this->organization->owner_uuid,
                    'created_at' => $this->organization->created_at,
                    'updated_at' => $this->organization->updated_at,
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testGetOrganizationsValidationError(): void
    {
        $this->get(route('organization.get'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetOrganizationsUnauthorized(): void
    {
        $this->get(route('organization.get'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testCreateOrganizationSuccess(): void
    {
        $data = [
            'name' => 'name',
            'city_uuid' => $this->city->uuid,
        ];

        $this->post(route('organization.create'), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($this->createdResponse([
                'name' => $data['name'],
                'owner_uuid' => $this->user->uuid,
                'city_uuid' => $data['city_uuid'],
            ]));
    }

    /**
     * @return void
     */
    public function testCreateOrganizationValidationError(): void
    {
        $this->post(route('organization.create'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'name' => [],
                'city_uuid' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testCreateOrganizationUnauthorized(): void
    {
        $this->post(route('organization.create'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testCreateOrganizationForbidden(): void
    {
        $this->user->assignRole(User::ROLE_TRAINER)->removeRole(User::ROLE_ADMIN);
        $this->post(route('organization.create'), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson($this->forbiddenResponse(self::MESSAGE_FORBIDDEN_ROLE));
    }
}

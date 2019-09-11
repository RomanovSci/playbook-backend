<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Playground;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class ScheduleTest
 * @package Tests\Api
 */
class ScheduleTest extends ApiTestCase
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
     * @var Schedule
     */
    protected $schedule;

    /**
     * @var Playground
     */
    protected $playground;

    /**
     * @var string
     */
    protected $startTime;

    /**
     * @var string
     */
    protected $endTime;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->startTime = Carbon::now()->toDateTimeString();
        $this->endTime = Carbon::now()->addHours(1)->toDateTimeString();
        $this->user = factory(User::class)->create([
            'password' => bcrypt('1111'),
            'status' => User::STATUS_ACTIVE,
            'verification_code' => '111111',
        ]);
        $this->user->assignRole(User::ROLE_TRAINER);
        $this->schedule = factory(Schedule::class)->create([
            'schedulable_uuid' => $this->user->uuid,
            'schedulable_type' => User::class,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $this->playground = factory(Playground::class)->create([
            'creator_uuid' => $this->user->uuid,
        ]);
        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken,
        ];
    }

    /**
     * @return void
     */
    public function testGetSchedulesSuccess(): void
    {
        $this->call(
            'GET',
            route('schedule.get', ['schedule_type' => 'trainer', 'uuid' => $this->user->uuid]),
            [
                'limit' => 100,
                'offset' => 0,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->schedule->uuid,
                    'start_time' => $this->startTime . ' +00:00',
                    'end_time' => $this->endTime . ' +00:00',
                    'price_per_hour' => $this->schedule->price_per_hour,
                    'currency' => $this->schedule->currency,
                    'created_at' => $this->schedule->created_at,
                    'updated_at' => $this->schedule->updated_at,
                    'confirmed_bookings' => [],
                    'playgrounds' => [],
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testGetSchedulesEmptySuccess(): void
    {
        $this->call(
            'GET',
            route('schedule.get', ['schedule_type' => 'trainer', 'uuid' => $this->user->uuid]),
            [
                'limit' => 100,
                'offset' => 0,
                'start_time' => Carbon::now()->addDays(99)->toDateTimeString(),
                'end_time' => Carbon::now()->addDays(100)->toDateTimeString(),
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse());
    }

    /**
     * @return void
     */
    public function testGetSchedulesValidationError(): void
    {
        $this->call(
            'GET',
            route('schedule.get', ['schedule_type' => 'trainer', 'uuid' => $this->user->uuid]),
            [
                'limit' => 100,
                'offset' => 0,
                'start_time' => Carbon::now()->addDays(1)->toDateTimeString(),
                'end_time' => Carbon::now()->toDateTimeString(),
            ]
        )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse());
    }

    /**
     * @return void
     */
    public function testCreateScheduleSuccess(): void
    {
        $data = [
            'dates' => [
                [
                    'start_time' => Carbon::now()->addDays(2)->toDateTimeString(),
                    'end_time' => Carbon::now()->addDays(3)->toDateTimeString(),
                ]
            ],
            'price_per_hour' => 7000,
            'currency' => 'USD',
            'playgrounds' => [(string) $this->playground->uuid]
        ];

        $this->post(route('schedule.create', ['schedulable_type' => 'trainer']), $data, $this->authorizationHeader)
            ->assertJson($this->createdResponse([
                [
                    'start_time' => $data['dates'][0]['start_time'] . ' +00:00',
                    'end_time' => $data['dates'][0]['end_time'] . ' +00:00',
                    'price_per_hour' => $data['price_per_hour'],
                    'currency' => $data['currency'],
                    'playgrounds' => [
                        [
                            'uuid' => $this->playground->uuid->toString(),
                            'name' => $this->playground->name,
                            'description' => $this->playground->description,
                            'address' => $this->playground->address,
                            'opening_time' => $this->playground->opening_time,
                            'closing_time' => $this->playground->closing_time,
                            'status' => $this->playground->status,
                            'type_uuid' => $this->playground->type_uuid,
                            'organization_uuid' => $this->playground->organization_uuid,
                            'creator_uuid' => $this->playground->creator_uuid,
                            'created_at' => $this->playground->created_at->toDateTimeString(),
                            'updated_at' => $this->playground->updated_at->toDateTimeString(),
                        ]
                    ]
                ]
            ]));
    }
}
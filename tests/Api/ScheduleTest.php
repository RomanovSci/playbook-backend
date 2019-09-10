<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Playground;
use App\Models\Schedule;
use App\Models\TrainerInfo;
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
        $this->schedule = factory(Schedule::class)->create([
            'schedulable_uuid' => $this->user->uuid,
            'schedulable_type' => User::class,
        ]);

        $this->authorizationHeader = [
            'Authorization' => 'Bearer ' . $this->user->createToken('TestToken')->accessToken
        ];
    }
}

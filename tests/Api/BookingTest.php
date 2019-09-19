<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\ApiTestCase;

/**
 * Class BookingTest
 * @package Tests\Api
 */
class BookingTest extends ApiTestCase
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
     * @var Booking
     */
    protected $booking;

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
        $this->booking = factory(Booking::class)->create([
            'bookable_type' => User::class,
            'bookable_uuid' => $this->user->uuid,
            'creator_uuid' => $this->user->uuid,
            'status' => Booking::STATUS_CREATED,
        ]);
    }

    /**
     * @return void
     */
    public function testGetBookingsForTrainerOrPlaygroundSuccess(): void
    {
        $this->get(route('booking.get', [
            'bookable_type' => 'trainer',
            'uuid' => $this->user->uuid,
            'limit' => 1,
            'offset' => 0,
            'start_time' => Carbon::create(2000)->toDateTimeString(),
            'end_time' => Carbon::create(3000)->toDateTimeString(),
        ]), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->booking->uuid->toString(),
                    'creator_uuid' => $this->booking->creator_uuid,
                    'start_time' => $this->booking->start_time->toDateTimeString(),
                    'end_time' => $this->booking->end_time->toDateTimeString(),
                    'note' => $this->booking->note,
                    'price' => $this->booking->price,
                    'currency' => $this->booking->currency,
                    'status' => $this->booking->status,
                    'playground_uuid' => $this->booking->playground_uuid,
                    'players_count' => $this->booking->players_count,
                    'created_at' => $this->booking->created_at->toDateTimeString(),
                    'updated_at' => $this->booking->updated_at->toDateTimeString(),
                    'equipments_rent' => [],
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testGetBookingsForTrainerOrPlaygroundValidationError(): void
    {
        $this->get(
            route('booking.get', ['bookable_type' => 'trainer', 'uuid' => $this->user->uuid,]),
            $this->authorizationHeader
        )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
                'start_time' => [],
                'end_time' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetBookingsForTrainerOrPlaygroundUnauthorized(): void
    {
        $this->get(route('booking.get', ['bookable_type' => 'trainer', 'uuid' => $this->user->uuid,]))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testGetUserBookingsSuccess(): void
    {
        $this->get(route('booking.get_user_bookings', [
            'limit' => 1,
            'offset' => 0,
            'start_time' => Carbon::create(2000)->toDateTimeString(),
            'end_time' => Carbon::create(3000)->toDateTimeString(),
        ]), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                [
                    'uuid' => $this->booking->uuid->toString(),
                    'creator_uuid' => $this->booking->creator_uuid,
                    'start_time' => $this->booking->start_time->toDateTimeString(),
                    'end_time' => $this->booking->end_time->toDateTimeString(),
                    'note' => $this->booking->note,
                    'price' => $this->booking->price,
                    'currency' => $this->booking->currency,
                    'status' => $this->booking->status,
                    'playground_uuid' => $this->booking->playground_uuid,
                    'players_count' => $this->booking->players_count,
                    'created_at' => $this->booking->created_at->toDateTimeString(),
                    'updated_at' => $this->booking->updated_at->toDateTimeString(),
                    'equipments_rent' => [],
                ]
            ]));
    }

    /**
     * @return void
     */
    public function testGetUserBookingsValidationError(): void
    {
        $this->get(route('booking.get_user_bookings'), $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'limit' => [],
                'offset' => [],
                'start_time' => [],
                'end_time' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testGetUserBookingsUnauthorized(): void
    {
        $this->get(route('booking.get_user_bookings'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

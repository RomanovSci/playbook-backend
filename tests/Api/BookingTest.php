<?php
declare(strict_types = 1);

namespace Tests\Api;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
     * @var Schedule
     */
    protected $schedule;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

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
        $this->schedule = factory(Schedule::class)->create([
            'schedulable_uuid' => $this->user->uuid,
            'schedulable_type' => User::class,
        ]);
        $this->bookingRepository = new BookingRepository();
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

    /**
     * @return void
     */
    public function testCreateBookingSuccess(): void
    {
        $data = [
            'start_time' => $this->schedule->start_time->toDateTimeString(),
            'end_time' => $this->schedule->end_time->toDateTimeString(),
            'bookable_uuid' => $this->user->uuid,
        ];

        $this->post(route('booking.create', ['bookable_type' => 'trainer']), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($this->createdResponse([
                'bookable_uuid' => $this->user->uuid,
                'bookable_type' => 'trainer',
                'creator_uuid' => $this->user->uuid,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'note' => null,
                'price' => 0,
                'currency' => 'USD',
                'status' => Booking::STATUS_CREATED,
                'players_count' => 1,
                'playground_uuid' => null,
            ]));
    }

    /**
     * @return void
     */
    public function testCreateBookingWithoutSchedule(): void
    {
        $data = [
            'start_time' => Carbon::now()->addDays(1)->toDateTimeString(),
            'end_time' => Carbon::now()->addDays(2)->toDateTimeString(),
            'bookable_uuid' => $this->user->uuid,
        ];

        $this->post(route('booking.create', ['bookable_type' => 'trainer']), $data, $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse(null, __('errors.schedule_time_unavailable')));
    }

    /**
     * @return void
     */
    public function testCreateBookingValidationError(): void
    {
        $this->post(route('booking.create', ['bookable_type' => 'trainer']), [], $this->authorizationHeader)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse([
                'start_time' => [],
                'end_time' => [],
                'bookable_uuid' => [],
            ]));
    }

    /**
     * @return void
     */
    public function testCreateBookingUnauthorized(): void
    {
        $this->post(route('booking.create', ['bookable_type' => 'trainer']))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }

    /**
     * @return void
     */
    public function testConfirmBookingSuccess(): void
    {
        /** Remove all bookings except current */
        $this->bookingRepository->builder()
            ->where('uuid', '!=', $this->booking->uuid->toString())
            ->delete();

        $this->post(
            route('booking.confirm', ['booking' => $this->booking->uuid->toString()]),
            [],
            $this->authorizationHeader
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($this->successResponse([
                'uuid' => $this->booking->uuid->toString(),
                'bookable_uuid' => $this->booking->bookable_uuid,
                'bookable_type' => 'trainer',
                'creator_uuid' => $this->booking->creator_uuid,
                'start_time' => $this->booking->start_time->toDateTimeString(),
                'end_time' => $this->booking->end_time->toDateTimeString(),
                'note' => $this->booking->note,
                'price' => $this->booking->price,
                'currency' => $this->booking->currency,
                'status' => Booking::STATUS_CONFIRMED,
                'players_count' => $this->booking->players_count,
                'playground_uuid' => $this->booking->playground_uuid,
                'created_at' => $this->booking->created_at->toDateTimeString(),
            ]));
    }

    /**
     * @return void
     */
    public function testConfirmBookingForbidden(): void
    {
        /**
         * @var User $anotherUser
         * @var Booking $anotherBooking
         */
        $anotherUser = factory(User::class)->create([
            'password' => bcrypt('1111'),
            'status' => User::STATUS_ACTIVE,
            'verification_code' => '111111',
        ]);
        $anotherBooking = factory(Booking::class)->create([
            'bookable_type' => User::class,
            'bookable_uuid' => $anotherUser->uuid,
            'creator_uuid' => $anotherUser->uuid,
            'status' => Booking::STATUS_CREATED,
        ]);

        $this->post(
            route('booking.confirm', ['booking' => $anotherBooking->uuid->toString()]),
            [],
            $this->authorizationHeader
        )
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson($this->forbiddenResponse(__('errors.cant_confirm_booking')));
    }

    /**
     * @return void
     */
    public function testConfirmBookingForBusyTime(): void
    {
        $this->post(
            route('booking.confirm', ['booking' => $this->booking->uuid->toString()]),
            [],
            $this->authorizationHeader
        )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson($this->errorResponse(null, __('errors.booking_time_busy')));
    }

    /**
     * @return void
     */
    public function ConfirmBookingUnauthorized(): void
    {
        $this->post(route('booking.confirm', ['booking' => $this->booking->uuid->toString()]))
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson($this->unauthorizedResponse());
    }
}

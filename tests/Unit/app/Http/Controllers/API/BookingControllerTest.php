<?php

namespace Tests\Unit\app\Http\Controllers\API;

use App\Services\Booking\BookingAvailabilityChecker;
use Tests\Mocks\app\Http\Controllers\API\BookingControllerMock;
use Tests\TestCase;

/**
 * Class BookingControllerTest
 *
 * @package Tests\Unit\app\Http\Controllers\API
 */
class BookingControllerTest extends TestCase
{
    public function testConstruct()
    {
        /** @var BookingAvailabilityChecker $bookingAvailabilityChecker */
        $bookingAvailabilityChecker = $this->getMockBuilder(BookingAvailabilityChecker::class)->getMock();
        $controller = new BookingControllerMock($bookingAvailabilityChecker);
        $this->assertInstanceOf(BookingAvailabilityChecker::class, $controller->bookingAvailabilityChecker);
    }
}

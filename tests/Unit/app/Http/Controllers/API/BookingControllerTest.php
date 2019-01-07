<?php

namespace Tests\Unit\app\Http\Controllers\API;

use App\Services\BookingService;
use Tests\Mocks\app\Http\Controllers\API\BookingControllerMock;
use Tests\TestCase;

/**
 * Class BookingControllerTest
 * @package Tests\Unit\app\Http\Controllers\API
 */
class BookingControllerTest extends TestCase
{
    public function testConstruct()
    {
        /** @var BookingService $bookingService */
        $bookingService = $this->getMockBuilder(BookingService::class)->getMock();
        $controller = new BookingControllerMock($bookingService);
        $this->assertInstanceOf(BookingService::class, $controller->bookingService);
    }
}

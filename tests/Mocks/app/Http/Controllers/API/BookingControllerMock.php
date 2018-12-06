<?php

namespace Tests\Mocks\app\Http\Controllers\API;

use App\Http\Controllers\API\BookingController;

/**
 * Class BookingControllerMock
 *
 * @package Tests\Mocks\app\Http\Controllers\API
 */
class BookingControllerMock extends BookingController
{
    public $bookingAvailabilityChecker;
}

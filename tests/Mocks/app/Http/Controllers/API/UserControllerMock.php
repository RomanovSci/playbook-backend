<?php

namespace Tests\Mocks\app\Http\Controllers\API;

use App\Http\Controllers\API\UserController;

/**
 * Class UserControllerMock
 *
 * @package Tests\Mocks\app\Http\Controllers\API
 */
class UserControllerMock extends UserController
{
    public $smsDeliveryService;
}
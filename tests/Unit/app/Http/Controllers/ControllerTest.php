<?php

namespace Tests\Unit\app\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Tests\Mocks\app\Http\Controllers\ControllerMock;
use Tests\TestCase;

/**
 * Class ControllerTest
 * @package Tests\Unit\app\Http\Controllers
 */
class ControllerTest extends TestCase
{
    public function testSuccess()
    {
        /**
         * @var ControllerMock $controller
         */
        $controller = $this->getMockBuilder(ControllerMock::class)->getMock();
        $actualResult = $controller->success();
        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }

    public function testError()
    {
        /**
         * @var ControllerMock $controller
         */
        $controller = $this->getMockBuilder(ControllerMock::class)->getMock();
        $actualResult = $controller->error(404);
        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }
}

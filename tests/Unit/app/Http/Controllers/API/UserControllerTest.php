<?php

namespace Tests\Unit\app\Http\Controllers\API;

use App\Http\Controllers\API\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class UserControllerTest
 *
 * @package Tests\Unit\app\Http\Controllers\API
 */
class UserControllerTest extends TestCase
{
    public function testLogout()
    {
        /**
         * @var UserController $controller
         */
        $controller = $this->getMockBuilder(UserController::class)
            ->disableOriginalConstructor()
            ->setMethods(['success'])
            ->getMock();
        $controller->expects($this->any())
            ->method('success')
            ->willReturn(new JsonResponse());

        /**
         * @var Request $request
         */
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['user'])
            ->getMock();
        $request->expects($this->any())
            ->method('user')
            ->willReturn(new class {
                public function token() {
                    return new class {
                        public function revoke() {
                            return true;
                        }
                    };
                }
            });

        $actualResult = $controller->logout($request);
        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }
}

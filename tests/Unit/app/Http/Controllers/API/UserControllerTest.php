<?php

namespace Tests\Unit\app\Http\Controllers\API;

use App\Http\Controllers\API\UserController;
use App\Http\Requests\BaseFormRequest;
use App\Http\Requests\User\UserCreateFormRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Class UserControllerTest
 *
 * @package Tests\Unit\app\Http\Controllers\API
 */
class UserControllerTest extends TestCase
{
    public function testRegister()
    {
        /**
         * @var UserCreateFormRequest $formRequestMock
         */
        $formRequestMock = $this->getMockBuilder(UserCreateFormRequest::class)
            ->setMethods(['all'])
            ->getMock();

        $formRequestMock->expects($this->any())
            ->method('all')
            ->willReturn([
                'first_name' => 'Test',
                'last_name' => 'Test',
                'password' => 'test',
                'phone' => 911,
            ]);

        /**
         * @var UserController $controller
         */
        $controller = $this->getMockBuilder(UserController::class)
            ->setMethods(['success'])
            ->getMock();
        $controller->expects($this->once())
            ->method('success')
            ->willReturn(new JsonResponse());


        $actualResult = $controller->register($formRequestMock);
        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }
}

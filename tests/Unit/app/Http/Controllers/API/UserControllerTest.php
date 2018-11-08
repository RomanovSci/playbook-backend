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
        $formRequestMock->expects($this->once())
            ->method('all')
            ->willReturn(['password' => 'test']);

        /**
         * @var User $user
         */
        $user = \Mockery::mock(User::class);
        $user->shouldReceive('create')
            ->once()
            ->with()
            ->andReturn(new class {
                public function assignRole() {
                    return true;
                }

                public function createToken() {
                    return 'token';
                }
            });

        /**
         * @var UserController $controller
         */
        $controller = $this->getMockBuilder(UserController::class)->getMock();
        $actualResult = $controller->register($formRequestMock);

//        $this->assertInstanceOf(JsonResponse::class, $actualResult);
    }
}

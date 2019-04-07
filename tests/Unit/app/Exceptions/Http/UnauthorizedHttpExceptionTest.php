<?php

namespace Tests\Unit\app\Exceptions\Http;

use App\Exceptions\Http\UnauthorizedHttpException;
use Tests\TestCase;

/**
 * Class UnauthorizedHttpExceptionTest
 * @package Tests\Unit\app\Exceptions\Http
 */
class UnauthorizedHttpExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new UnauthorizedHttpException();
        $this->assertEquals(401, $exception->getStatusCode());
        $this->assertEquals('Unauthorized', $exception->getMessage());
    }
}

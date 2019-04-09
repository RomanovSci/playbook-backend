<?php

namespace Tests\Unit\app\Exceptions\Http;

use App\Exceptions\Http\ForbiddenHttpException;
use Tests\TestCase;

/**
 * Class ForbiddenHttpExceptionTest
 * @package Tests\Unit\app\Exceptions\Http
 */
class ForbiddenHttpExceptionTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new ForbiddenHttpException();
        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals('Forbidden', $exception->getMessage());
    }
}

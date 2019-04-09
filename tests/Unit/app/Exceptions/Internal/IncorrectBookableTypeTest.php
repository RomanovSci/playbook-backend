<?php

namespace Tests\Unit\app\Exceptions\Internal;

use App\Exceptions\Internal\IncorrectBookableType;
use Tests\TestCase;

/**
 * Class IncorrectBookableTypeTest
 * @package Tests\Unit\app\Exceptions\Http
 */
class IncorrectBookableTypeTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new IncorrectBookableType();
        $this->assertEquals('Incorrect bookable type', $exception->getMessage());
    }
}

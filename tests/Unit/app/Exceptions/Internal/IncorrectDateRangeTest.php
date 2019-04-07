<?php

namespace Tests\Unit\app\Exceptions\Internal;

use App\Exceptions\Internal\IncorrectDateRange;
use Tests\TestCase;

/**
 * Class IncorrectDateRangeTest
 * @package Tests\Unit\app\Exceptions\Http
 */
class IncorrectDateRangeTest extends TestCase
{
    public function testConstruct()
    {
        $exception = new IncorrectDateRange();
        $this->assertEquals('Incorrect date range', $exception->getMessage());
    }
}

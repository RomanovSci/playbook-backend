<?php

namespace App\Arrays;

use App\Models\Schedule;

/**
 * Class ArrayOfSchedule
 *
 * @package App\Arrays
 */
class ArrayOfSchedule extends \ArrayObject
{
    /**
     * @param mixed $key
     * @param mixed $val
     * @return true
     */
    public function offsetSet($key, $val): bool
    {
        if ($val instanceof Schedule) {
            parent::offsetSet($key, $val);
            return true;
        }

        throw new \InvalidArgumentException('Value must be a Schedule');
    }
}

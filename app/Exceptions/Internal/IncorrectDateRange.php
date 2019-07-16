<?php
declare(strict_types = 1);

namespace App\Exceptions\Internal;

/**
 * Class IncorrectDateRange
 * @package App\Exceptions\Internal
 */
class IncorrectDateRange extends \Exception
{
    protected $message = 'Incorrect date range';
}

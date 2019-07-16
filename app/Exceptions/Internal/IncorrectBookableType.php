<?php
declare(strict_types = 1);

namespace App\Exceptions\Internal;

/**
 * Class IncorrectBookableType
 * @package App\Exceptions\Internal
 */
class IncorrectBookableType extends \Exception
{
    protected $message = 'Incorrect bookable type';
}

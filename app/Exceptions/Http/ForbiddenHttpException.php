<?php
declare(strict_types = 1);

namespace App\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ForbiddenHttpException
 * @package App\Exceptions\Http
 */
class ForbiddenHttpException extends HttpException
{
    public function __construct(
        string $message = '',
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct(403, $message ?: 'Forbidden', $previous, $headers, $code);
    }
}

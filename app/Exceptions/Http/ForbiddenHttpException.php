<?php

namespace App\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ForbiddenHttpException
 * @package App\Exceptions\Http
 */
class ForbiddenHttpException extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct(403, $message ?? 'Forbidden', $previous, $headers, $code);
    }
}

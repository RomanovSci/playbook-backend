<?php

namespace App\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException as BaseUnauthorizedHttpException;

/**
 * Class UnauthorizedHttpException
 * @package App\Exceptions\Http
 */
class UnauthorizedHttpException extends BaseUnauthorizedHttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        ?int $code = 0,
        array $headers = array()
    ) {
        parent::__construct('Bearer', $message ?? 'Unauthorized', $previous, $code, $headers);
    }
}

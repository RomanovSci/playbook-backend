<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Exceptions\Http\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
class Authenticate extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     * @throws \Exception
     */
    protected function authenticate($request, array $guards)
    {
        try {
            return parent::authenticate($request, $guards);
        } catch (\Exception $e) {
            if ($e instanceof AuthenticationException) {
                throw new UnauthorizedHttpException();
            }

            throw $e;
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request): string
    {
        return route('index');
    }
}

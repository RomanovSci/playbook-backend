<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Http\Request;
use Closure;

/**
 * Class HorizonAuthBasic
 * @package App\Http\Middleware
 */
class HorizonAuthBasic extends AuthenticateWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @param string $field
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        return $this->auth->guard('web')->basic('phone') ?: $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\Http\UnauthorizedHttpException;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 * Class RoleMiddleware
 * @package App\Http\Middleware
 */
class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (Auth::guest()) {
            throw new UnauthorizedHttpException();
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        if (! Auth::user()->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}

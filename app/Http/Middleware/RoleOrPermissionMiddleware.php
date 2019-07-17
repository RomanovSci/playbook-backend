<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Exceptions\Http\UnauthorizedHttpException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

/**
 * Class RoleOrPermissionMiddleware
 * @package App\Http\Middleware
 */
class RoleOrPermissionMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $roleOrPermission
     * @return mixed
     */
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (Auth::guest()) {
            throw new UnauthorizedHttpException();
        }

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        try {
            if (!Auth::user()->hasAnyRole($rolesOrPermissions) ||
                !Auth::user()->hasAnyPermission($rolesOrPermissions)
            ) {
                throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
            }
        } catch (PermissionDoesNotExist $exception) {
        }

        return $next($request);
    }
}

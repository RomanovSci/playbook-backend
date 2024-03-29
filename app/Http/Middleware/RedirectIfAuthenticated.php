<?php
declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Exceptions\Http\ForbiddenHttpException;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class RedirectIfAuthenticated
 * @package App\Http\Middleware
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if ($guard === 'api') {
                throw new ForbiddenHttpException();
            }

            return redirect('/');
        }

        return $next($request);
    }
}

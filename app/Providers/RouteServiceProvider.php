<?php
declare(strict_types = 1);

namespace App\Providers;

use App\Models\Playground;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class RouteServiceProvider
 * @package App\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    protected const UUID_PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
    protected const SCHEDULABLE_PATTERN = 'trainer|playground';

    protected const ROUTE_PATTERNS = [
        self::UUID_PATTERN => [
            'uuid',
            'user',
            'booking',
            'schedule',
            'info',
            'playground'
        ],
        self::SCHEDULABLE_PATTERN => [
            'bookable_type',
            'schedulable_type'
        ],
    ];

    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->initRoutePatterns();
        parent::boot();
        $this->initRouteBinds();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * @return void
     */
    protected function initRoutePatterns(): void
    {
        $patterns = [];

        foreach (self::ROUTE_PATTERNS as $pattern => $keys) {
            foreach ($keys as $key) {
                $patterns[$key] = $pattern;
            }
        }

        Route::patterns($patterns);
    }

    /**
     * @return void
     */
    protected function initRouteBinds(): void
    {
        $schedulableChecker = function ($value) {
            return $value === 'trainer' ? User::class : Playground::class;
        };

        Route::bind('bookable_type', $schedulableChecker);
        Route::bind('schedulable_type', $schedulableChecker);
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}

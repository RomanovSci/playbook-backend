<?php
declare(strict_types = 1);

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function authorization(): void
    {
        Horizon::auth(function ($request) {
            return app()->environment('local') || in_array($request->ip(), [
                '172.22.0.1',
                '185.38.209.242'
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Horizon::night();
    }
}

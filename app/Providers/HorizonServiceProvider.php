<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function authorization()
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
    public function register()
    {
        // Horizon::night();
    }
}

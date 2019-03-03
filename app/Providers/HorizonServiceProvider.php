<?php

namespace App\Providers;

use Laravel\Horizon\Horizon;
use Illuminate\Support\Facades\Gate;
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

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function authorization()
    {
        Horizon::auth(function ($request) {
            return app()->environment('local');
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

<?php

namespace App\Providers;

use App\Services\SmsDelivery\SmsDeliveryServiceInterface;
use App\Services\SmsDelivery\SmsDeliveryServiceMobizon;
use Illuminate\Support\ServiceProvider;

/**
 * Class SmsDeliveryServiceProvider
 * @package App\Providers
 */
class SmsDeliveryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SmsDeliveryServiceInterface::class,
            SmsDeliveryServiceMobizon::class
        );
    }
}

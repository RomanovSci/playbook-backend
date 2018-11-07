<?php

namespace App\Providers;

use App\Services\SmsDeliveryService\SmsDeliveryServiceInterface;
use App\Services\SmsDeliveryService\SmsDeliveryServiceMobizon;
use Illuminate\Support\ServiceProvider;

/**
 * Class SmsDeliveryServiceProvider
 *
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

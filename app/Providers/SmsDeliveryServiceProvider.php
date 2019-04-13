<?php

namespace App\Providers;

use App\Services\SmsDelivery\Providers\SmsRuProvider;
use App\Services\SmsDelivery\SmsDeliveryInterface;
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
        $this->app->bind(SmsDeliveryInterface::class, SmsRuProvider::class);
    }
}

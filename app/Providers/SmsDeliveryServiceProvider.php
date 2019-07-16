<?php
declare(strict_types = 1);

namespace App\Providers;

use App\Services\SmsDelivery\Providers\Twilio;
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
    public function boot(): void
    {
       //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(SmsDeliveryInterface::class, Twilio::class);
    }
}

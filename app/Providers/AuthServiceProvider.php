<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Organization;
use App\Models\Playground;
use App\Models\Schedule\Schedule;
use App\Models\TrainerInfo;
use App\Policies\BookingPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PlaygroundPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\TrainerInfoPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Organization::class => OrganizationPolicy::class,
        Playground::class => PlaygroundPolicy::class,
        Schedule::class => SchedulePolicy::class,
        Booking::class => BookingPolicy::class,
        TrainerInfo::class => TrainerInfoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /** Gates */
        Gate::define('getBookingsList', 'App\Policies\BookingPolicy@getBookingsList');

        /** Passport setup */
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    }

    /**
     * @inheritdoc
     * @return voidphp artisan vendor:publish --tag=passport-migrations
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }
}

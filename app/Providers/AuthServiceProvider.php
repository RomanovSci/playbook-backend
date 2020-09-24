<?php
declare(strict_types = 1);

namespace App\Providers;

use App\Models\Booking;
use App\Models\Organization;
use App\Models\Playground;
use App\Models\Schedule;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\TrainerInfo;
use App\Policies\BookingPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PlaygroundPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\TournamentPlayerPolicy;
use App\Policies\TournamentPolicy;
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
        Tournament::class => TournamentPolicy::class,
        TournamentPlayer::class => TournamentPlayerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
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
     * @return void
     */
    public function register(): void
    {
        Passport::ignoreMigrations();
    }
}

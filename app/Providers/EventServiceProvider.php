<?php

namespace App\Providers;

use App\Events\User\RegisterUserEvent;
use App\Events\User\ResetPasswordEvent;
use App\Listeners\User\RegisterUserListener;
use App\Listeners\User\ResetPasswordListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ResetPasswordEvent::class => [ResetPasswordListener::class],
        RegisterUserEvent::class => [RegisterUserListener::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}

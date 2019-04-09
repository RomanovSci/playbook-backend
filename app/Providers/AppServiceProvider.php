<?php

namespace App\Providers;

use App\Models\Playground;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Uppercase validation
         */
        Validator::extend('uppercase', function ($attribute, $value) {
            return strtoupper($value) === $value;
        }, 'The :attribute must be uppercase.');

        /**
         * Is correct currency validation
         */
        Validator::extend('currency', function ($attribute, $value) {
            return in_array($value, array_keys(config('money')));
        }, 'Attribute :attribute is invalid currency.');

        /**
         * Bookable exists validation
         */
        Validator::extend('bookable_exists', function ($attribute, $value) {
            return User::where('uuid', $value)->exists() || Playground::where('uuid', $value)->exists();
        }, 'Bookable does not exists.');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

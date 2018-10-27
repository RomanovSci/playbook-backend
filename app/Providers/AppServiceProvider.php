<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('uppercase', function($attribute, $value) {
            return strtoupper($value) === $value;
        }, 'The :attribute must be uppercase.');

        Validator::extend('currency', function($attribute, $value) {
            return in_array($value, array_keys(config('money')));
        }, 'Attribute :attribute is invalid currency');
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

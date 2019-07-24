<?php
declare(strict_types = 1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * @inheritdoc
     * @return void
     */
    protected function authorization(): void
    {
        Horizon::auth(function (Request $request) {
            return app()->environment('local') || $request->user('web')->hasRole([User::ROLE_ADMIN]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Horizon::night();
    }
}

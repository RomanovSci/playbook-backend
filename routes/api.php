<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', 'API\UserController@register')->name('user.register');
Route::middleware(['role:' . User::ROLE_ORGANIZATION_ADMIN . '|'. User::ROLE_ADMIN])->group(function () {
    /** Organization */
    Route::prefix('organization')->group(function () {
        Route::post('/create', 'API\OrganizationController@create')
            ->name('organization.create');
    });

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::get('/all', 'API\PlaygroundController@all')
            ->name('playground.all');
        Route::post('/create/{organization}', 'API\PlaygroundController@create')
            ->name('playground.create');
    });

    /** Playground schedule */
    Route::prefix('playground-schedule')->group(function () {
        Route::post('/create/{playground}', 'API\PlaygroundScheduleController@create')
            ->name('playground-schedule.create');
    });
});

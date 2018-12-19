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
Route::post('/login', 'API\UserController@login')->name('user.login');

/**
 * Schedules
 */
Route::get('/schedule/{schedulable_type}/{id?}', 'API\ScheduleController@get')
    ->where(['schedulable_type' => 'trainer|playground'])
    ->name('schedule.get');

/**
 * Authenticated user
 */
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', 'API\UserController@logout')->name('user.logout');
    Route::post('/phone-verify', 'API\UserController@verifyPhone')->name('user.verifyPhone');

    /** Booking */
    Route::prefix('booking')->group(function () {
        Route::post('/{bookable_type}/create', 'API\BookingController@create')
            ->where(['bookable_type' => 'trainer|playground'])
            ->name('booking.create');
    });
});

/**
 * All roles, except user
 */
Route::middleware(['role:'
    . User::ROLE_ADMIN . '|'
    . User::ROLE_ORGANIZATION_ADMIN . '|'
    . User::ROLE_TRAINER
])->group(function () {
    /** Booking */
    Route::prefix('booking')->group(function () {
        Route::post('/confirm/{booking}', 'API\BookingController@confirm')
            ->name('booking.confirm');
    });

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::get('/search', 'API\PlaygroundController@search')
            ->name('playground.search');

        Route::post('/create', 'API\PlaygroundController@create')
            ->name('playground.create');
    });

    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/{schedulable_type}/create', 'API\ScheduleController@create')
            ->where(['schedulable_type' => 'trainer|playground'])
            ->name('schedule.create');

        Route::delete('/delete/{schedule}', 'API\ScheduleController@delete')
            ->name('schedule.delete');
    });
});

/**
 * Role organization admin and system admin
 */
Route::middleware(['role:'
    . User::ROLE_ADMIN . '|'
    . User::ROLE_ORGANIZATION_ADMIN
])->group(function () {
    /** Organization */
    Route::prefix('organization')->group(function () {
        Route::post('/create', 'API\OrganizationController@create')
            ->name('organization.create');
    });
});

/**
 * Role trainer and system admin
 */
Route::middleware(['role:'
    . User::ROLE_ADMIN . '|'
    . User::ROLE_TRAINER
])->group(function () {
    /** Trainer info */
    Route::prefix('trainer')->group(function () {
        Route::post('/info-create', 'API\UserController@createTrainerInfo')
            ->name('user.createTrainerInfo');
    });
});

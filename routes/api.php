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
 * Schedule
 */
Route::get('/schedule/{type}', 'API\ScheduleController@get')
    ->where(['type' => 'trainer|playground'])
    ->name('schedule.get');

/**
 * Authenticated user
 */
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', 'API\UserController@logout')->name('user.logout');

    /** Booking */
    Route::prefix('booking')->group(function () {
        Route::post('/create', 'API\BookingController@create')
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

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::post('/create', 'API\PlaygroundController@create')
            ->name('playground.create');
    });

    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/playground/create', 'API\ScheduleController@createForPlayground')
            ->name('schedule.playground.create');
    });
});

/**
 * Role trainer and system admin
 */
Route::middleware(['role:'
    . User::ROLE_ADMIN . '|'
    . User::ROLE_TRAINER
])->group(function () {
    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/trainer/create', 'API\ScheduleController@createForTrainer')
            ->name('schedule.trainer.create');
    });

    /** Trainer info */
    Route::prefix('trainer')->group(function () {
        Route::post('/info-create', 'API\UserController@createTrainerInfo')
            ->name('user.createTrainerInfo');
    });
});

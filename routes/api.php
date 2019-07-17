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
/** User */
Route::post('/resend_verification_code', 'API\UserController@resendVerificationCode')->name('user.resendVerificationCode');
Route::post('/reset_password', 'API\UserController@resetPassword')->name('user.resetPassword');

/** Schedule */
Route::prefix('schedule')->group(function () {
    Route::get('/{schedulable_type}/{uuid?}', 'API\ScheduleController@get')->name('schedule.get');
});

/** Trainer */
Route::prefix('trainer')->group(function () {
    Route::get('/', 'API\TrainerController@get')->name('trainer.get');
    Route::get('/{user}/info', 'API\TrainerController@getInfo')->name('trainer.getInfo');
});

/** Equipment */
Route::prefix('equipment')->group(function () {
    Route::get('/{bookable_type}/{uuid}', 'API\EquipmentController@get')->name('equipment.get');
});

/**
 * Only guest routes
 */
Route::middleware(['guest:api'])->group(function () {
    Route::post('/register', 'API\UserController@register')->name('user.register');
    Route::post('/login', 'API\UserController@login')->name('user.login');
});

/**
 * Authenticated user
 */
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', 'API\UserController@logout')->name('user.logout');
    Route::post('/phone_verify', 'API\UserController@verifyPhone')->name('user.verifyPhone');

    /** City */
    Route::prefix('city')->group(function () {
        Route::get('/', 'API\CityController@get')->name('city.get');
        Route::get('/search', 'API\CityController@search')->name('city.search');
    });

    /** Country */
    Route::prefix('country')->group(function () {
        Route::get('/', 'API\CountryController@get')->name('country.get');
        Route::get('/search', 'API\CountryController@search')->name('country.search');
    });

    /** Booking */
    Route::prefix('booking')->group(function () {
        Route::get('/all', 'API\BookingController@getUserBookings')->name('booking.getUserBookings');
        Route::post('/{bookable_type}', 'API\BookingController@create')->name('booking.create');
        Route::post('/{booking}/decline', 'API\BookingController@decline')->name('booking.decline');
    });

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::get('/', 'API\PlaygroundController@get')->name('playground.get');
        Route::get('/search', 'API\PlaygroundController@search')->name('playground.search');
        Route::get('/types', 'API\PlaygroundController@getTypes')->name('playground.getTypes');
    });

    /** Organization */
    Route::prefix('organization')->group(function () {
        Route::get('/', 'API\OrganizationController@get')->name('organization.get');
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
        Route::post('/{booking}/confirm', 'API\BookingController@confirm')->name('booking.confirm');
        Route::get('/{bookable_type}/{uuid}', 'API\BookingController@get')->name("booking.get");
    });

    /** Equipment */
    Route::prefix('equipment')->group(function () {
        Route::post('/', 'API\EquipmentController@create')->name('equipment.create');
    });

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::post('/', 'API\PlaygroundController@create')->name('playground.create');
    });

    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/{schedulable_type}', 'API\ScheduleController@create')->name('schedule.create');
        Route::put('/{schedule}', 'API\ScheduleController@edit')->name('schedule.edit');
        Route::delete('/{schedule}', 'API\ScheduleController@delete')->name('schedule.delete');
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
        Route::post('/', 'API\OrganizationController@create')->name('organization.create');
    });
});

/**
 * Role trainer and system admin
 */
Route::middleware(['role:'
    . User::ROLE_ADMIN . '|'
    . User::ROLE_TRAINER
])->group(function () {
    /** Trainer */
    Route::prefix('trainer')->group(function () {
        Route::post('/info', 'API\TrainerController@createInfo')->name('trainer.createInfo');
        Route::put('/info/{info}', 'API\TrainerController@editInfo')->name('trainer.editInfo');
    });
});

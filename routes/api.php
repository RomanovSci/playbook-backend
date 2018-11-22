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
 * Authenticated user
 */
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', 'API\UserController@logout')->name('user.logout');
});

/**
 * Role organization admin and system admin
 */
Route::middleware(['role:' . User::ROLE_ADMIN . '|'. User::ROLE_ORGANIZATION_ADMIN])->group(function () {
    /** Organization */
    Route::prefix('organization')->group(function () {
        Route::post('/create', 'API\OrganizationController@create')
            ->name('organization.create');
    });

    /** Playground */
    Route::prefix('playground')->group(function () {
        Route::post('/create/{organization}', 'API\PlaygroundController@create')
            ->name('playground.create');
    });

    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/create-for-playground/{playground}', 'API\ScheduleController@createForPlayground')
            ->name('schedule.createForPlayground');
    });
});

/**
 * Role trainer and system admin
 */
Route::middleware(['role:' . User::ROLE_ADMIN . '|'.  User::ROLE_TRAINER])->group(function () {
    /** Schedule */
    Route::prefix('schedule')->group(function () {
        Route::post('/create-for-trainer', 'API\ScheduleController@createForTrainer')
            ->name('schedule.createForTrainer');
    });

    /** Trainer info */
    Route::prefix('trainer-info')->group(function () {
        Route::post('/create', 'API\TrainerInfoController@create')
            ->name('trainerInfo.create');
    });
});

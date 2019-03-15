<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/{any?}', function (Request $request) {
    if (View::exists('generated.index') && !$request->is('api/*')) {
        return view('generated.index');
    }

    throw new NotFoundHttpException();
})->where('any', '.*')->name('index');

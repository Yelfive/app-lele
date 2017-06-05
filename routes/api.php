<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'User'], function () {
        Route::namespace('User')->get('user', 'ProfileController@profile')->name('user.profile');
    });

});

Route::group(['middleware' => 'api'], function () {
    Route::group(['namespace' => 'Auth'], function () {
        // User
        Route::put('user', 'LoginController@login');
        Route::post('user', 'RegisterController@register');
    });
});
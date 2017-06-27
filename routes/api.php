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
        Route::get('user', 'ProfileController@profile')->name('user.profile');
        Route::put('user/logout', 'LogoutController@logout');
        Route::put('user/password', 'PasswordController@reset');
        Route::put('user/profile', 'ProfileController@edit');
        Route::put('user/coordinate', 'CoordinateController@update');

        Route::get('user/{id}/profile', 'HomepageController@profile');
    });

    Route::group(['namespace' => 'Friends'], function () {
        Route::post('friends/request', 'RequestController@send');
        Route::put('friends/request', 'RequestController@agree');
        Route::delete('friends/request', 'RequestController@decline');
        Route::get('friends/request', 'RequestController@index');
        Route::get('friends/nearby', 'NearbyController@search');

        Route::get('friends', 'ListController@index');
    });

});

Route::group(['middleware' => 'api'], function () {
    Route::group(['namespace' => 'Auth'], function () {
        // User
        Route::put('user', 'LoginController@login');
        Route::post('user', 'RegisterController@register');
    });

    Route::group(['namespace' => 'Supports'], function () {
        Route::get('state/code', 'StateController@index');
        Route::get('verify-code/sms', 'VerifyCodeController@sms');
    });
});
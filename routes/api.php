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

        Route::get('user/{id}/profile', 'HomepageController@profile');
    });

    Route::group(['namespace' => 'Friends'], function () {
        Route::post('friend/request', 'AddController@send');
        Route::put('friend/request', 'AddController@agree');
        Route::delete('friend/request', 'AddController@decline');
        Route::get('friend/request', 'AddController@index');

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
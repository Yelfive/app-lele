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
    Route::get('user', 'UserController@profile')->name('user.profile');

    // Top up
    Route::get('top-up', 'TopUpController@history');
    Route::post('top-up', 'TopUpController@charge');

    // coupon
    Route::get('coupon', 'CouponController@list');

    Route::group(['namespace' => 'Pay'], function () {
        Route::post('pay', 'PayController@pay');
    });

    Route::get('express', 'ExpressController@query');
});

Route::group(['middleware' => 'api'], function () {
    Route::group(['namespace' => 'Sinopec'], function () {
        Route::post('indoor-buy/points', 'IndoorController@synchronizePoints');
    });

    Route::group(['namespace' => 'Pay'], function () {
        Route::get('order/paid', 'PayController@checkOrderPaid');
    });

    Route::group(['namespace' => 'Auth'], function () {
        // User
        Route::put('user', 'LoginController@login');
        Route::post('user', 'RegisterController@register');
    });
});
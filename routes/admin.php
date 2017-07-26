<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-24
 */

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Account'], function () {
    Route::get('/', 'LoginController@index')->name('login.page');
    Route::post('session', 'LoginController@login');

    Route::get('session/delete', 'LogoutController@logout');

});

Route::group(['middleware' => 'auth:admin'], function () {
    Route::group(['namespace' => 'User'], function () {
        Route::get('users', 'IndexController@index');
        Route::get('user/{id}', 'ViewController@view');
        Route::post('user/{id}/delete', 'ViewController@delete');
    });

    Route::get('settings', 'SettingController@display');

});

<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-28
 */

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Sinopec'], function () {
    Route::any('test', 'TestController@index');
});

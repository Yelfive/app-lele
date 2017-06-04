<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-24
 */


use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Pay'], function () {

    Route::group(['prefix' => 'notify'], function () {
        Route::post('ali-pay.php', 'NotifyController@aliPay');
    });

});


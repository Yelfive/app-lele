<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-25
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;

class Controller extends \App\Http\Controllers\Controller
{
    public function __construct()
    {
        Auth::setDefaultDriver('admin');
    }
}
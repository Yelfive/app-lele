<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-25
 */

namespace App\Http\Controllers\Admin;

class SiteController extends Controller
{
    public function index()
    {
        return view('site/index');
    }
}
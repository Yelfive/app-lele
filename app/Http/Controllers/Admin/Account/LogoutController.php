<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-26
 */

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout()
    {
        Session::flush();
        return redirect()->to('admin');
    }
}
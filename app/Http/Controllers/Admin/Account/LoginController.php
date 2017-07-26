<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-24
 */

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function index()
    {
        return view('admin/login');
    }

    protected function username()
    {
        return 'username';
    }

    public function authenticated(Request $request, $user)
    {
        return redirect()->to('admin/users');
    }
}
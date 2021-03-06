<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace app\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use fk\ease\mob\IM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends ApiController
{
    public function logout(IM $IM)
    {
        $IM->logout(Auth::user()->im_account);
        Session::flush();
        $this->result->message('注销成功');// TODO: i18n
    }
}
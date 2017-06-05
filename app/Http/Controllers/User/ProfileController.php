<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;

class ProfileController extends ApiController
{
    public function profile()
    {
        $this->result
            ->message('获取用户信息成功')
            ->data(Auth::user()->getProfile());
    }

}
<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-05
 */

namespace App\Http\Controllers\User;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends ApiController
{
    public function profile()
    {
        $this->result
            ->message('获取用户信息成功')
            ->data(Auth::user()->getProfile());
    }

    public function edit(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'file',
        ]);

        $user = Auth::user();
        $user->fill($request->input());
        if ($password = $request->input('password')) {
            $user->password_hash = Hash::make($password);
        } else if ($avatar = $request->file('avatar')) {
            $user->saveAvatar($avatar);
        }

        if ($user->update()) {
            $this->result
                ->message('更新成功')
                ->data($user->getProfile());
        } else {
            $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('更新成功')
                ->extend(['errors' => $user->errors->toArray()]);
        }
    }

}
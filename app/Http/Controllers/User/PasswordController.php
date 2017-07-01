<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-24
 */

namespace app\Http\Controllers\User;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Supports\VerifyCodeController;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class PasswordController extends ApiController
{
    public function reset(Request $request)
    {
        $this->validateData($request->input(), [
            'mobile' => 'required|string|size:11',
            'verify_code' => 'required|string|size:6',
            'password' => 'required|string',
        ]);

        if ($this->checkVerifyCode()) {
            $user = Auth::user();
            if ($user->update(['password_hash' => Hash::make($request->input('password'))])) {
                //
                return $this->result->message('更新密码成功');
            } else {
                $this->result->message('更新密码失败')
                    ->extend(['errors' => $user->errors->toArray()]);
            }
        } else {
            $this->result->message('验证码错误');
        }

        $this->result->code(HttpStatusCode::CLIENT_VALIDATION_ERROR);

    }

    protected function checkVerifyCode(): bool
    {
        return VerifyCodeController::check(
            VerifyCodeController::FOR_RESET_PASSWORD,
            $this->request->get('mobile'),
            $this->request->get('verify_code')
        );
    }
}
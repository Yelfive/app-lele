<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace app\Http\Controllers\Auth;

use App\Components\ApiResult;
use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
//use fk\utility\Http\Request;
use App\Models\Order;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends ApiController
{
    use AuthenticatesUsers;

    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|size:11',
            'password' => 'required|string'
        ]);
    }

    public function username()
    {
        return 'mobile';
    }

    protected function credentials(Request $request)
    {
        return $request->only(['mobile', 'password']);
    }

    protected function sendLoginResponse()
    {
        $data = $this->getUser()->getProfile();

        $this->result
            ->data($data)
            ->extend(['access_token' => Session::getId()])
            ->message(__('user.Signed in successfully.'));
    }

    protected function sendLockoutResponse()
    {

    }

    protected function sendFailedLoginResponse($request)
    {
        $this->result->code(HttpStatusCode::CLIENT_INVALID_LOGIN)
            ->message(__('user.Login failed.'))
            ->extend(['data_received' => $request->all()]);
    }

    /**
     * @return \App\Models\User
     */
    protected function getUser()
    {
        return Auth::user();
    }

}
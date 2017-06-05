<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace app\Http\Controllers\Auth;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use App\Models\User;
use fk\utility\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends ApiController
{
    use RegistersUsers;

    protected function validator($data)
    {
        return Validator::make($data, [
            'mobile' => 'required|string|size:11',
            'password' => 'required|string|min:6',
        ]);
    }

    protected function getCity()
    {
        return [
            '5001',
            '成都'
        ];
    }

    protected function create($data)
    {
        list ($cityCode, $cityName) = $this->getCity();
        /** @var User $user */
        $user = new User(array_merge($data, [
            'password_hash' => Hash::make($data['password']),
            'it_says' => '',
            'address' => '',
            'city_name' => $cityCode,
            'city_code' => $cityName,
        ]));

        $user->setAccount();

        if ($user->validate() && $user->save()) {
            $this->result->message('注册成功')
                ->data($user->getProfile());
        } else {
            $this->result->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('注册失败')
                ->extend(['errors' => $user->errors->toArray()]);
        }
        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        if ($user->hasErrors()) return $this->result;

        event(new Registered($user));

        $this->guard()->login($user);
        if (Auth::guest()) {
            $this->result->extend(['error' => '账号创建成功,自动登录失败,请尝试手动登录']);
        } else {
            $this->result->extend(['access_token' => Session::getId()]);
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return bool
     */
    protected function registered(Request $request, $user)
    {
        return !$user->hasErrors();
    }

}
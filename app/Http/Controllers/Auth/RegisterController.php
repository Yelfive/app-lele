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
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
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
            $this->result->message('注册成功');
        } else {
            $this->result->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('注册失败')
                ->extend(['errors' => $user->errors->toArray()]);
        }
        return $user;
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
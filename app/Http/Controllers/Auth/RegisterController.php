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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterController extends ApiController
{
    use RegistersUsers;

    /**
     * @var Request
     */
    protected $request;

    protected function validator($data)
    {
        return Validator::make($data, [
            'mobile' => 'required|string|size:11',
            'password' => 'required|string|min:6',
            'verify_code' => 'required|string|size:6',
            'avatar' => 'file',
            'state_code' => 'integer',
            'age' => 'integer|min:1|max:200',
            'address' => 'string',
            'it_says' => 'string',
        ]);
    }

    protected function getCity()
    {
        return [
            '5001',
            '成都'
        ];
    }

    protected function checkVerifyCode()
    {
        // TODO: unfinished
        return true;
    }

    protected function create($data)
    {
        if (!$this->checkVerifyCode()) {
            return $this->result
                ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('短信验证码不正确');
        }
        list ($cityCode, $cityName) = $this->getCity();

        $attributes = array_filter(array_merge($data, [
            'password_hash' => Hash::make($data['password']),
            'it_says' => $this->request->input('it_says'),
            'sex' => $this->request->input('sex'),
            'address' => $this->request->input('address'),
            'avatar' => '',
            'city_name' => $cityName,
            'city_code' => $cityCode,
        ]), function ($v) {
            return $v !== null;
        });

        /** @var User $user */
        $user = new User($attributes);

        $user->setAccount();

        DB::beginTransaction();
        if ($user->validate() && $user->save() && !$user->hasErrors()) {
            DB::commit();
            $user->saveAvatar($this->request->file('avatar'));
            $user->update();
            $this->result
                ->message('注册成功')
                ->data($user->getProfile());
        } else {
            DB::rollBack();
            $this->result->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                ->message('注册失败')
                ->extend(['errors' => $user->errors->toArray()]);
        }
        return $user;
    }

    /**
     * @param string $as
     * @return string Relative path to `/storage`
     */
    protected function uploadAvatar($as)
    {
        $uploadedFile = $this->request->file('avatar');
        if ($uploadedFile) {
            $filename = $uploadedFile->storeAs('images/avatar', $as . '.' . $uploadedFile->extension());
            return basename($filename);
        } else {
            return '';
        }
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
        $this->request = $request;
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
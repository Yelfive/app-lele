<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-24
 */

namespace App\Http\Controllers\Supports;

use App\Components\HttpStatusCode;
use App\Components\Messenger;
use App\Http\Controllers\ApiController;
use fk\messenger\SendFailedException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VerifyCodeController extends ApiController
{

    const CACHE_PREFIX = 'verify_code_';

    const SCENARIO_REGISTER = 1;
    const SCENARIO_RESET_PASSWORD = 2;

    const TEMPLATE_REGISTER = 'SMS_75870028';
    CONST TEMPLATE_RESET_PASSWORD = 'SMS_75765043';

    protected $forge = false;

    protected $config;

    public function sms(Messenger $messenger)
    {
        $this->validateData($this->request->query->all(), [
            'mobile' => 'required|string|size:11',
            'scenario' => ['required', Rule::in($this->scenarios())]
        ]);
        $scenario = $this->request->get('scenario');
        $mobile = $this->request->get('mobile');
        if ($this->forge) {
            $this->result->extend([
                'verify_code' => $code = $this->generateCode()
            ]);
        } else {
            $this->config = $messenger->with;
            $data = $this->getContent($scenario, $code);
            try {
                $messenger->send($mobile, $data);
            } catch (SendFailedException $e) {
                $this->result
                    ->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                    ->message('验证码获取失败');
            }
        }

        Cache::add(static::CACHE_PREFIX . "{$scenario}_{$mobile}", $code, 600);
        $this->result->message('验证码获取成功');
    }

    protected function getContent($for, &$code)
    {
        if ($this->forge) return $this->generateCode();

        $code = $this->generateCode();
        $app = $this->config['app'];

        switch ($for) {
            case static::SCENARIO_REGISTER:
                return "验证码{$code}，您正在注册{$app}，感谢您的支持！";
            case static::SCENARIO_RESET_PASSWORD:
                return "验证码{$code}，您正在修改{$app}密码，感谢您的支持！";
        }
    }

    protected function generateCode()
    {
        if ($this->forge) return '123456';

        return (string)mt_rand(100000, 999999);
    }

    protected function scenarios()
    {
        return [
            static::SCENARIO_REGISTER, static::SCENARIO_RESET_PASSWORD,
        ];
    }

    public static function check($scenario, $mobile, $code): bool
    {
        $key = static::CACHE_PREFIX . "{$scenario}_{$mobile}";
        if ($code && Cache::get($key) == $code) {
            Cache::forget($key);
            return true;
        }
        return false;
    }
}
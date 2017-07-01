<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-24
 */

namespace App\Http\Controllers\Supports;

use App\Components\HttpStatusCode;
use App\Http\Controllers\ApiController;
use fk\messenger\Messenger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VerifyCodeController extends ApiController
{

    const CACHE_PREFIX = 'verify_code_';

    const SCENARIO_REGISTER = 1;
    const SCENARIO_RESET_PASSWORD = 2;

    const TEMPLATE_REGISTER = 'SMS_75870028';
    CONST TEMPLATE_RESET_PASSWORD = 'SMS_75765043';

    protected $forge = true;

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
            $success = true;
        } else {
            $this->config = config('sms.AliDaYu');
            $content = $this->getContent($scenario);
            $success = $messenger->with($this->config)->send($mobile, $content);
            $code = $content['params']['code'];
        }

        if ($success) {
            Cache::add(static::CACHE_PREFIX . "{$scenario}_{$mobile}", $code, 600);
            $this->result->message('验证码获取成功');
        } else {
            $this->result
                ->code(HttpStatusCode::SERVER_THIRD_PARTY_ERROR)
                ->message('验证码获取失败');
        }
    }

    protected function getContent($for)
    {
        if ($this->forge) return $this->generateCode();

        $signature = $this->config['signature'];

        switch ($for) {
            case static::SCENARIO_RESET_PASSWORD:
                return [
                    'signature' => $signature,
                    'template' => static::TEMPLATE_RESET_PASSWORD,
                    'params' => [
                        'code' => $code = $this->generateCode(),
                        'app' => $signature
                    ],
                    'message' => "验证码{$code}，您正在修改乐乐密码，感谢您的支持！",
                ];
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
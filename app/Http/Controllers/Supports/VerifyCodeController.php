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

    const FOR_REGISTER = 1;
    const FOR_RESET_PASSWORD = 2;

    const TEMPLATE_REGISTER = 'SMS_75870028';
    CONST TEMPLATE_RESET_PASSWORD = 'SMS_75765043';

    protected $forge = true;

    protected $config;

    public function sms(Messenger $messenger)
    {
        $this->validateData($this->request->query->all(), [
            'mobile' => 'required|string|size:11',
            'for' => ['required', Rule::in($this->fors())]
        ]);
        $for = $this->request->get('for');
        $mobile = $this->request->get('mobile');
        if ($this->forge) {
            $this->result->extend([
                'verify_code' => $code = $this->generateCode()
            ]);
            $success = true;
        } else {
            $this->config = config('sms.AliDaYu');
            $content = $this->getContent($for);
            $success = $messenger->with($this->config)->send($mobile, $content);
            $code = $content['params']['code'];
        }

        if ($success) {
            Cache::add("code_{$for}_{$mobile}", $code, 600);
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
            case static::FOR_RESET_PASSWORD:
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

    protected function fors()
    {
        return [
            static::FOR_REGISTER, static::FOR_RESET_PASSWORD,
        ];
    }

    public static function check($for, $mobile, $code): bool
    {
        return $code && Cache::get("verify_code_{$for}_{$mobile}") == $code;
    }
}
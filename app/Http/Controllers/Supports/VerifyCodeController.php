<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-24
 */

namespace App\Http\Controllers\Supports;

use App\Http\Controllers\ApiController;
use fk\messenger\Messenger;

class VerifyCodeController extends ApiController
{
    public function sms(Messenger $messenger)
    {

        $messenger->send();
        $this->result->message('验证码获取成功');
    }
}
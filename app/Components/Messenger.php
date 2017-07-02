<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-03
 */

namespace App\Components;

use fk\messenger\Messenger as MessengerBase;

class Messenger extends MessengerBase
{
    public $with;

    public function __construct()
    {
        $this->with = config('sms.' . config('sms.default'));
    }
}
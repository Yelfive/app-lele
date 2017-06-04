<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-20
 */

namespace App\Components;

use Illuminate\Http\Response;

class ApiResponse extends Response
{
    public function __construct($result = '', $status = 200, $headers = array())
    {
        parent::__construct($result, $status, $headers);
        if ($result instanceof ApiResult) {
            $this->setStatusCode($result->code);
        }
    }
}
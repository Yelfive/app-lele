<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-08
 */

namespace App\Providers;

use Illuminate\Support\Facades\App;

class SessionServiceProvider extends \fk\utility\Session\SessionServiceProvider
{
    public function getAccessToken()
    {
        if (false == $request = $this->app['request']) return null;
        return $request->server('HTTP_X_ACCESS_TOKEN', null);
    }
}
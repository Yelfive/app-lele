<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-02
 */

namespace App\Listeners;

use App\Events\UserUpdated;
use Illuminate\Support\Facades\Auth;

class UpdateIdentity
{
    public function handle(UserUpdated $userUpdated)
    {
        $user = $userUpdated->model;
        if ($user->id && Auth::id() && $user->id == Auth::id()) {
            Auth::setUser($user);
        }
    }
}
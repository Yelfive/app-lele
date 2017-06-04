<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\UserInfo;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUserProfile
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated $event
     * @throws \Exception
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $info = new UserInfo();
        $info->id = $user->id;
        $info->fill([
            'idcard_front' => '',
            'idcard_back' => '',
            'tp_tables' => UserInfo::TP_TABLES_NONE,
        ]);
        if (!$info->save()) {
            throw new \Exception('Failed saving');
        }

    }
}

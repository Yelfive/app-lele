<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserCreating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateIMAccount
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param UserCreating $event
     */
    public function handle(UserCreating $event)
    {
        $user = $event->user;
        $user->im_account = '123';
        $user->im_password = 'abc';
    }
}

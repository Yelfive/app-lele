<?php

namespace App\Listeners;

use App\Events\UserCreating;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateLeLeNo
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
     * @param  UserCreating $event
     * @return void
     */
    public function handle(UserCreating $event)
    {
        $user = $event->user;

        $user->account = $user->generateAccount();
        if (null === $user->it_says) $user->it_says = '';
    }

}

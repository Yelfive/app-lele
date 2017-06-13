<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserCreating;
use fk\ease\mob\IM;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateIMAccount
{
    protected $IM;

    /**
     * Create the event listener.
     *
     */
    public function __construct(IM $IM)
    {
        $this->IM = $IM;
    }

    /**
     * Handle the event.
     *
     * @param UserCreated $event
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $user->generateIMAccount();
        $this->IM->userRegister($user->im_account, $user->im_password, $user->nickname);
        $user->update();
    }
}

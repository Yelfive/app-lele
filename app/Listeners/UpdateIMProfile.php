<?php

namespace App\Listeners;

use App\Events\UserSaved;
use fk\ease\mob\IM;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateIMProfile
{
    /**
     * @var IM
     */
    public $im;

    /**
     * Create the event listener.
     * @param IM $IM
     */
    public function __construct(IM $IM)
    {
        $this->im = $IM;
    }

    /**
     * Handle the event.
     *
     * @param  UserSaved $event
     * @return void
     */
    public function handle(UserSaved $event)
    {
        $user = $event->user;
        if ($user->nickname !== $user->getOriginal('nickname')) {
            $this->im->userModifyNickname($user->im_account, $user->nickname);
        }
    }
}

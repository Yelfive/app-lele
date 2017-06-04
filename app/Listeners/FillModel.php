<?php

namespace App\Listeners;

use App\Events\ModelSaving;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class FillModel
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
     * @param  ModelSaving $event
     * @return bool
     */
    public function handle(ModelSaving $event)
    {
        $model = $event->model;
        if (null != $model::CREATED_BY && null === $model->{$model::CREATED_BY} && $uid = Auth::id()) {
            $model->{$model::CREATED_BY} = $uid;
        }
        return $model->validate();
    }
}

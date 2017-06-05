<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\{
    ModelSaving, UserCreated, UserCreating, UserUpdated
};
use App\Listeners\{
    CreateUserProfile, FillModel, UpdateIdentity, GenerateLeLeNo
};

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [
            CreateUserProfile::class,
        ],
        ModelSaving::class => [
            FillModel::class,
        ],
        UserUpdated::class => [
            UpdateIdentity::class
        ],
        UserCreating::class => [
            GenerateLeLeNo::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

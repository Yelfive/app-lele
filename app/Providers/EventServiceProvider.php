<?php

namespace App\Providers;

use App\Models\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\{
    ModelSaving, UserCreated, UserCreating, UserUpdated, UserSaved
};
use App\Listeners\{
    CreateIMAccount, FillModel, UpdateIdentity, GenerateLeLeNo, SaveToMongo
};

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ModelSaving::class => [
            FillModel::class,
        ],
        UserSaved::class => [
            SaveToMongo::class
        ],
        UserUpdated::class => [
            UpdateIdentity::class
        ],
        UserCreating::class => [
            GenerateLeLeNo::class,
        ],
        UserCreated::class => [
            CreateIMAccount::class
        ],
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

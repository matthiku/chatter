<?php

namespace App\Providers;

use Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\NewUserRegistered',
        ],
        'App\Events\MessagePosted' => [
            'App\Listeners\NewMessageEmailNotification',
        ]
    ];

    /**
     * Register any (other) events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}

<?php

namespace App\Listeners;

use Mail;
use App\Mail\UserRegistered;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserRegistered
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // send a email to the Admin
        Mail::to(App\User::find(1))->send(new UserRegistered($event->user));
    }
}

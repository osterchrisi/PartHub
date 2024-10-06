<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserRegisteredWithPlan;

class SendVerificationEmailWithPlan
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredWithPlan $event)
    {
        $event->user->sendEmailVerificationNotification($event->plan, $event->priceId);
    }
}

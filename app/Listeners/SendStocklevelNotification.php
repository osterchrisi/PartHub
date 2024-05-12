<?php

namespace App\Listeners;

use App\Mail\StocklevelNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendStocklevelNotification
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
    public function handle(object $event): void
    {
        // Send Stock Level Notification Mail
        Mail::to($event->user)->bcc(env('MAIL_FROM_ADDRESS'))->send(new StocklevelNotification($event->user, $event->stock_levels));
    }
}

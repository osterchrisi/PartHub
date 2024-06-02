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
        // Extract the stock quantity from the stock levels array
        $resulting_quantity = $event->stock_levels[1];

        // Check if the stock quantity is below zero
        if ($resulting_quantity < 0) {
            // Send Stock Level Notification Mail
            Mail::to($event->user)->bcc(env('MAIL_FROM_ADDRESS'))->send(new StocklevelNotification($event->user, $event->stock_levels));
        }
    }

}

<?php

namespace App\Listeners;

use App\Mail\StocklevelNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\Part;


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
        // Extract the part ID and stock quantity from the stock levels array
        $part_id = $event->stock_levels[0];
        $stock_quantity = $event->stock_levels[1];

        // Get the part's notification threshold
        $part = Part::find($part_id);
        $notification_threshold = $part->stocklevel_notification_threshold;
        

        // Check if the stock quantity is below the threshold
        if ($stock_quantity < $notification_threshold) {
            // Send Stock Level Notification Mail
            Mail::to($event->user)->bcc(config('mail.from.address'))->send(new StocklevelNotification($event->user, $event->stock_levels));
        }
    }

}

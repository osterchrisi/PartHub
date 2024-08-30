<?php

namespace App\Listeners;

use App\Mail\StocklevelNotification;
use App\Models\Part;
use App\Services\UserSettingService;
use Illuminate\Support\Facades\Mail;

class SendStocklevelNotification
{
    protected $userSettingService;

    /**
     * Inject the UserSettingService into the listener
     */
    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
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
            // Check if the user wants a notification
            if ($this->userSettingService->shouldNotify($event->user->id)) {
                // Send Stock Level Notification Mail
                Mail::to($event->user)
                    ->bcc(config('mail.from.address'))
                    ->send(new StocklevelNotification($event->user, $event->stock_levels));
            }
        }
    }
}

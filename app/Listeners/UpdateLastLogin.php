<?php

namespace App\Listeners;

class UpdateLastLogin
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
        // Update last login timestamp
        $event->user->update(['last_login_at' => now()]);
    }
}

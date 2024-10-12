<?php

namespace App\Providers;

use App\Events\StockMovementOccured;
use App\Listeners\SendStocklevelNotification;
use App\Listeners\StripeEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\UpdateLastLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // New registered User
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Stock Movement occured
        StockMovementOccured::class => [
            SendStocklevelNotification::class,
        ],
        // Stripe Webhook
        WebhookReceived::class => [
            StripeEventListener::class,
        ],
        Login::class => [
            UpdateLastLogin::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

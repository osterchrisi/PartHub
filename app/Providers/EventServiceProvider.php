<?php

namespace App\Providers;

use App\Events\StockMovementOccured;
use App\Listeners\SendStocklevelNotification;
use App\Listeners\StripeEventListener;
use App\Listeners\UpdateLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

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
        //Quick solution to send an e-mail upon new user registrations
        parent::boot();

        // Register the listener directly as a closure
        Event::listen(Registered::class, function ($event) {
            $user = $event->user;
            $adminEmail = config('mail.admin_email');

            // Check if the admin email is set
            if (! $adminEmail) {
                // Log an error if the admin email is not configured
                \Log::error('Admin email is not set in configuration. Unable to send new user registration notification.');

                return;
            }

            // Send a plain text email
            Mail::raw(
                "A new user has registered:\n\nName: {$user->name}\nEmail: {$user->email}",
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject('New User Registered');
                }
            );
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

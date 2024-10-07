<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;
use App\Models\User;



class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'customer.subscription.created') {
            Log::info('Handling customer.subscription.created');

            $subscription = $event->payload['data']['object'];
            $customerId = $subscription['customer'];
            $priceId = $subscription['items']['data'][0]['price']['id'];

            // Find the user by Stripe customer ID
            $user = User::where('stripe_id', $customerId)->first();

            if ($user) {
                // Check if the price ID matches the current database value
                if ($user->price_id !== $priceId) {
                    Log::warning("Price ID mismatch for user ID: {$user->id}. Database: {$user->price_id}, Stripe: {$priceId}");
                }

                // Set the selected plan and price ID to null
                $user->update([
                    'selected_plan' => null,
                    'price_id' => null,
                ]);

                Log::info("User subscription details have been set to null for user ID: {$user->id}");
            }
        }
    }
}
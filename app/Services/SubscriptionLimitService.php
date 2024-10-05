<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class SubscriptionLimitService
{
    public function hasReachedLimit($user, $resourceType)
    {
        // Get the user's current subscription plan
        $subscription = $user->subscription('maker');
        if (!$subscription) {
            $user->subscription = 'free';
        }

        Log::info($subscription);

        // Retrieve limits from the config file based on the subscription plan
        $limits = config('subscription_limits.' . $subscription->name);
        Log::info($limits);

        // Build dynamic method names based on the resource type
        //TODO: Figure out if it's a supplier data and give part_id
        $resourceCountMethod = 'get' . ucfirst($resourceType) . 'Count';

        // Check if the user has reached the limit for the specified resource type
        return $user->$resourceCountMethod() >= $limits[$resourceType . '_limit'];
    }
}

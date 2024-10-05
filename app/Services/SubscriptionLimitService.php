<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class SubscriptionLimitService
{
    public function hasReachedLimit($user, $resourceType, $part_id = null)
    {
        // Get the user's current subscription plan
        $subscription = $user->subscription('maker');
        if (!$subscription) {
            $subscriptionType = 'free';
        }
        else {
            $subscriptionType = $subscription->name;
        }

        Log::info('User is on ' . $subscription->name . ' plan.');

        // Retrieve limits from the config file based on the subscription plan
        $limits = config('subscription_limits.' . $subscriptionType);

        // Build dynamic method names based on the resource type
        $resourceCountMethod = ($resourceType === 'supplier_data')
            ? 'getSupplierDataCount'
            : 'get' . ucfirst($resourceType) . 'Count';

        // Check if the limit is null (unlimited)
        $limit = $limits[$resourceType . '_limit'];

        if (is_null($limit)) {
            // If the limit is null, it means "unlimited," so return before even checking
            return false;
        }

        // Call the appropriate method, passing $part_id for supplier_data or no argument for others
        if ($resourceType === 'supplier_data') {
            return $user->$resourceCountMethod($part_id) >= $limit;
        }
        else {
            return $user->$resourceCountMethod() >= $limit;
        }
    }

}

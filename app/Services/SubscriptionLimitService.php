<?php

namespace App\Services;

class SubscriptionLimitService
{
    public function hasReachedLimit($user, $resourceType, $part_id = null)
    {
        \Log::info('Checking subscription limits for user', ['user_id' => $user->id, 'resource_type' => $resourceType, 'part_id' => $part_id]);
        // Get the user's current subscription plan
        $subscription = $user->subscription('maker');
        if (! $subscription) {
            $subscriptionType = 'free';
        } else {
            $subscriptionType = $subscription->name;
        }

        // Retrieve limits from the config file based on the subscription plan
        $limits = config("subscription_limits.{$subscriptionType}");

        // Build dynamic method names based on the resource type
        $resourceCountMethod = ($resourceType === 'supplier_data')
            ? 'getSupplierDataCount'
            : 'get'.ucfirst($resourceType).'Count';

        if (! array_key_exists("{$resourceType}_limit", $limits)) {
            logger()->warning("No limit config found for resource type '{$resourceType}' in plan '{$subscriptionType}' (user ID: {$user->id})");

            return false;
        }

        // Check if the limit is null (unlimited)
        $limit = $limits["{$resourceType}_limit"];

        if (is_null($limit)) {
            // If the limit is null, it means "unlimited," so return before even checking
            return false;
        }

        // Call the appropriate method, passing $part_id for supplier_data or no argument for others
        if ($resourceType === 'supplier_data') {
            return $user->$resourceCountMethod($part_id) >= $limit;
        } else {
            return $user->$resourceCountMethod() >= $limit;
        }
    }
}

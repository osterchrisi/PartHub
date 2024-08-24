<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * This method registers the custom Blade directive '@subscribed', 
     * which checks if the authenticated user is subscribed to one or more plans.
     *
     * Usage in Blade template:
     *
     * @subscribed('plan1')
     *     <p>Content visible to users subscribed to plan1.</p>
     * @endsubscribed
     *
     * @subscribed(['plan1', 'plan2'])
     *     <p>Content visible to users subscribed to either plan1 or plan2.</p>
     * @endsubscribed
     *
     * @return void
     */
    public function boot(): void
    {
        Blade::if('subscribed', function ($plans) {
            if (!auth()->check()) {
                return false;
            }

            if (!is_array($plans)) {
                $plans = [$plans];
            }

            foreach ($plans as $plan) {
                if (auth()->user()->subscribed($plan)) {
                    return true;
                }
            }

            return false;
        });
    }
}

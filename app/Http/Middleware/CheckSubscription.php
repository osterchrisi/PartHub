<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        //! Disabled, doesn't actually do anything because still under development
        if ($user->hasNoSubscription()) {
            return $next($request);
            // return redirect()->route('subscription.index')->with('error', 'You need a subscription to access this feature.');
        }

        return $next($request);
    }
}

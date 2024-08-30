<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info('Executing RedirectIfNotAuthenticated Middleware');
        // Check if the user is not authenticated
        if (! Auth::check()) {
            // Log::info('User not authenticated. Redirecting to whatis.');
            // Redirect to /whatis route
            return redirect()->route('whatis');
        }

        // Log::info('User authenticated. Proceeding with request.');
        // If authenticated, allow request to proceed
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\UserSettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    protected $userSettingService;

    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Retrieve the user timezone from user settings
            $timezone = $this->userSettingService->getUserTimezone(Auth::id());

            // Set application's timezone to user timezone
            Config::set('app.timezone', $timezone);

            // Set PHP default timezone
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}

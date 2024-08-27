<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserSettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
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
     * @param  \Closure  $next
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
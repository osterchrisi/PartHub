<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SubscriptionLimitService;
use Symfony\Component\HttpFoundation\Response;

class CheckResourceLimits
{
    protected $limitService;

    public function __construct(SubscriptionLimitService $limitService)
    {
        $this->limitService = $limitService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info($request);
        $user = $request->user();
        $type = $request->input('type');  // Get the resource type from the request
        $part_id = $request->input('part_id') ? $request->input('part_id') : '';

        // Call the service to check the limit for the given resource type
        if ($this->limitService->hasReachedLimit($user, $type, $part_id)) {
            return response()->json([
                'message' => "You have reached your {$type} creation limit for your subscription plan."
            ], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionLimitService;
use Closure;
use Illuminate\Http\Request;
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
        $user = $request->user();
        $type = $request->input('type');  // Get the resource type from the request
        $part_id = $request->input('part_id') ? $request->input('part_id') : '';

        if ($this->limitService->hasReachedLimit($user, $type, $part_id)) {
            if ($type === 'supplier_data') {
                $type = 'supplier entries per Part';
            }
            $type = ucfirst($type);

            return response()->json([
                'message' => "You have reached your {$type} creation limit for your subscription plan.",
            ], 403);
        }

        return $next($request);
    }
}

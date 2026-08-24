<?php

namespace Modules\Log\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Log\Services\ActivityService;

readonly class TrackActivity{

	public function __construct(private readonly ActivityService $activityService){
	}

	/**
	 * Handle an incoming request.
	 */
	public function handle(Request $request, Closure $next){
		if (Auth::check()){
			$userId   = Auth::id();
			$cacheKey = "activity-tracked:{$userId}:" . now()->toDateString();

			if (!Cache::has($cacheKey)){
				$this->activityService->recordAccess($userId);
				Cache::put($cacheKey, TRUE, now()->addMinute());
			}
		}

		return $next($request);
	}
}

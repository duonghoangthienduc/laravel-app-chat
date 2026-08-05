<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity{

	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 */
	public function handle(Request $request, Closure $next)
	: Response{
		// Track activity here.
		$request->ip();
		$request->userAgent();
		Log::channel('user_activity')
		   ->info(
			   'User request', [
			   'user_id'    => $request->user()?->id,
			   'ip' => $request->ip(),
			   'remote_addr' => $request->server('REMOTE_ADDR'),
			   'cf_connecting_ip' => $request->header('CF-Connecting-IP'),
			   'x_forwarded_for' => $request->header('X-Forwarded-For'),
			   'x_real_ip' => $request->header('X-Real-IP'),
			   'method' => $request->method(),
			   'url' => $request->fullUrl(),
			   'user_agent' => $request->userAgent(),
		   ]);

		return $next($request);
	}
}

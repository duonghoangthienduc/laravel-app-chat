<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware{

	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 */
	public function handle(Request $request, Closure $next)
	: Response{
		$clientIp = $this->getClientIp($request);

		$blockedIps = [
//			"172.18.0.1",
			"115.78.6.171",
			"115.78.6.138"
		];

		if (in_array($clientIp, $blockedIps, TRUE)){
			abort(403, 'Access denied.');
		}

		return $next($request);
	}

	private function getClientIp(Request $request)
	: string{
		return $request->header('CF-Connecting-IP')
		       ?? $request->ip();
	}
}

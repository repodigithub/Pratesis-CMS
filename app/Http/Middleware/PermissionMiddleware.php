<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $p1 = null, $p2 = null, $p3 = null, $p4 = null)
    {
        $response = $next($request);
        if (auth()->user()->hasPermission($p1)) {
            return $response;
        }
        if (auth()->user()->hasPermission($p2)) {
            return $response;
        }
        if (auth()->user()->hasPermission($p3)) {
            return $response;
        }
        if (auth()->user()->hasPermission($p4)) {
            return $response;
        }
        throw new AccessDeniedHttpException("Access denied.");
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $g1 = null, $g2 = null, $g3 = null, $g4 = null)
    {
        $response = $next($request);
        if (auth()->user()->hasRole($g1)) {
            return $response;
        }
        if (auth()->user()->hasRole($g2)) {
            return $response;
        }
        if (auth()->user()->hasRole($g3)) {
            return $response;
        }
        if (auth()->user()->hasRole($g4)) {
            return $response;
        }
        throw new AccessDeniedHttpException("Access denied.");
    }
}

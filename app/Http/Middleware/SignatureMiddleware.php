<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action

        $response = $next($request);

        // Post-Middleware Action
        $url = $request->url();
        $signatures = collect();
        $timeout = ini_get('max_execution_time');

        for ($i = 0; $i < $timeout; $i++) {
            $timestamp = strtotime("-$i seconds");
            $signatures->push(hash_hmac("sha256", "$url|$timestamp", env("HMAC_SECRET")));
        }
        
        $authorization = $request->header("Authorization");
        if ($signatures->search($authorization) === false) {
            throw new UnauthorizedHttpException("", "Signature invalid.");
        }

        return $response;
    }
}

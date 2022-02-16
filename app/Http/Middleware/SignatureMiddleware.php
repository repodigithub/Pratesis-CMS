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
        $timestamp = time();
        $encrypted = hash_hmac("sha256", "$url|$timestamp", env("HMAC_SECRET"));
        $authorization = $request->header("Authorization");
        if ($encrypted != $authorization) {
            return response()->json(['status'=>'auth failed','auth'=>$authorization,'encrypt'=>$encrypted,'timestamp'=>$timestamp,'url'=>$url], 401);
        }

        return $response;
    }
}

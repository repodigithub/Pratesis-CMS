<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CorsMiddleware
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
    $headers = [
      "Access-Control-Allow-Methods" => "*",
      "Access-Control-Allow-Origin" => "*",
      "Access-Control-Allow-Headers" => "*",
      "Access-Control-Allow-Credentials" => true
    ];

    if ($request->isMethod("OPTIONS")) {
      return response()->json("", Response::HTTP_NO_CONTENT, $headers);
    }

    $response = $next($request);
    foreach ($headers as $key => $value) {
      $response->headers->set($key, $value);
    }

    return $response;
  }
}

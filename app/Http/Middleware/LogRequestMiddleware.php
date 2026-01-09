<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRequestMiddleware
{
  public function handle($request, Closure $next)
  {
    $start = microtime(true);
    $response = $next($request);
    $elapsed = round(microtime(true) - $start, 4);

    Log::info('HTTP Request', [
      'path' => $request->path(),
      'method' => $request->method(),
      'status' => $response->getStatusCode(),
      'elapsed_s' => $elapsed,
      'ip' => $request->ip(),
    ]);

    return $response;
  }
}

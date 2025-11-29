<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserIsVerified
{
  public function handle($request, Closure $next)
  {
    $user = $request->user();
    if (!$user) {
      return response()->json(['message' => 'Unauthenticated'], 401);
    }
    if (!$user->is_verified) {
      return response()->json(['message' => 'Account not verified'], 403);
    }
    return $next($request);
  }
}

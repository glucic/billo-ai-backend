<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiting
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = optional($request->user())->id ?: $request->ip();
        
        $limiter = app(RateLimiter::class);
        
        if ($limiter->tooManyAttempts($key, env('RATE_LIMIT_ATTEMPTS', 60))) {
            return response()->json([
                'message' => 'Too many requests',
                'retry_after' => $limiter->availableIn($key)
            ], 429);
        }
        
        $limiter->hit($key, env('RATE_LIMIT_MINUTES', 1) * 60);
        
        return $next($request);
    }
}
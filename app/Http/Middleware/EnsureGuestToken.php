<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EnsureGuestToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is authenticated, allow access
        if (Auth::check()) {
            return $next($request);
        }

        // Check for guest token
        if (!$request->header('X-Guest-Token')) {
            return response()->json([
                'success' => false,
                'message' => 'Guest token is required'
            ], 400);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the bearer token from the request header
        $token = $request->bearerToken();

        // Check if the token is provided
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401); // Unauthorized
        }

        // Verify the token using Sanctum
        $user = Auth::guard('sanctum')->user();

        // Check if the token is valid
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401); // Unauthorized
        }

        // Allow the request to proceed
        return $next($request);
    }
}

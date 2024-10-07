<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
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
        // Assuming the user role is stored in a 'role' attribute
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request); // Allow access if the user is an admin
        }

        return response()->json(['error' => 'Unauthorized'], 403); // Deny access
    }
}

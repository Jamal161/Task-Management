<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // if (Auth::user() && Auth::user()->is_admin) {
        //     return $next($request);
        // }

        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request); // Allow access if the user is an admin
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}

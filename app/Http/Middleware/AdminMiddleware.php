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
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user has the role of "administrator"
            if (Auth::user()->role === 'administrator') {
                // Allow the request to proceed to the next middleware
                return $next($request);
            }
        }

        // If the user is not authenticated or does not have the role of "administrator",
        // redirect them to the dashboard with an error message
        return redirect()->route('403');
    }
}

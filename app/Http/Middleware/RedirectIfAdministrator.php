<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdministrator
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and is an administrator
        if (Auth::check() && Auth::user()->role === 'administrator') {
            // Redirect administrators to the admin dashboard route
            return redirect()->route('stats');
        }

        // Allow regular users to access the requested route
        return $next($request);
    }
}

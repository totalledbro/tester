<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAnggota
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and is not an anggota
        if (Auth::check() && Auth::user()->role !== 'anggota') {
            // Redirect non-anggota users to the dashboard
            return redirect()->route('dash');
        }

        // Allow anggota users to access the requested route
        return $next($request);
    }
}

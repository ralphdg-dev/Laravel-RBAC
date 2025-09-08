<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        if (Auth::user()->role !== $role) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
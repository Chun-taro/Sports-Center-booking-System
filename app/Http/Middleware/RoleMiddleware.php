<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (! in_array($request->user()->role, $roles)) {
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($request->user()->isStaff()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}

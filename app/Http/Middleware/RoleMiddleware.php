<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request based on user role boundaries.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (! in_array($user->role, $roles)) {
            if ($user->isCustomer()) {
                return redirect()->route('home')->with('error', 'Access Denied: Customer accounts cannot access management features.');
            }

            if ($user->isStaff()) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: Staff members cannot access administrator settings.');
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: Unauthorized section.');
            }

            return redirect()->route('home')->with('error', 'Access Denied: Unauthorized access.');
        }

        return $next($request);
    }
}

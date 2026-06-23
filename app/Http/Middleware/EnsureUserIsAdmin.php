<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Allowed roles for the Admin Panel: 2=admin, 3=manager, 4=support.
     * Role 1 (customer) is redirected to the homepage.
     */
    private const ALLOWED_ROLES = [2, 3, 4];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! in_array((int) $user->role, self::ALLOWED_ROLES, true)) {
            return redirect()->route('home')->with('error', 'You do not have permission to access the Admin Panel.');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {

            Log::info('UpdateLastSeen executed', [
                'user_id' => auth()->id(),
                'username' => auth()->user()->username,
            ]);

            auth()->user()->update([
                'last_seen_at' => now(),
            ]);
        }

        return $next($request);
    }
}
<?php

namespace Swapnil\StarterKit\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StarterMiddleware extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function handle($request, \Closure $next, ...$guards)
    {
        if(Auth::check()) {
            return $next($request);
        }
        return redirect()->route('login');
    }
}

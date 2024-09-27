<?php

namespace Swapnil\StarterKit\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StarterMiddleware extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request The incoming request.
     * @return string|null The redirect path or null if the request expects JSON.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request.
     * @param \Closure $next The next middleware to be called.
     * @param string ...$guards Additional guards to be checked.
     * @return mixed The response or a redirect to the login route if the user is not authenticated.
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if(Auth::check()) {
            return $next($request);
        }
        return redirect()->route('login');
    }
}

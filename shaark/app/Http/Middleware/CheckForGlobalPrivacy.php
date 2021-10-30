<?php

namespace App\Http\Middleware;

use Closure;

class CheckForGlobalPrivacy
{
    public function handle($request, Closure $next)
    {
        if (app('shaark')->authorizeFromRequest($request)) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}

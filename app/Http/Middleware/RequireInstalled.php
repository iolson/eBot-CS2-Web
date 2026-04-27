<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.installed')) {
            return $next($request);
        }

        // Allow the install wizard itself through
        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        return redirect('/install');
    }
}

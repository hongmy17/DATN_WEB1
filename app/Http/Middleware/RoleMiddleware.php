<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (int) auth()->user()->role !== 1) {
            abort(404);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Bypass ngrok browser warning
 * Ngrok hiện cảnh báo "You are about to visit..." chặn load CSS/JS
 * Header này tắt cảnh báo đó đi
 */
class NgrokBypass
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('ngrok-skip-browser-warning', 'true');
        return $response;
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotAdminForWrite
{
    /**
     * Block write methods for admin (read-only).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $method = strtoupper($request->getMethod());

        if ($user && $user->role === 'admin' && !in_array($method, ['GET', 'HEAD'], true)) {
            abort(403);
        }

        return $next($request);
    }
}

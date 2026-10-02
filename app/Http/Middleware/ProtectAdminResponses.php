<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectAdminResponses
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->is('admin', 'admin/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackRequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(
            'request_context',
            $request->path()
        );

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        // Telescope-specific CSP exception for local development only.
        $isLocalTelescope = app()->environment('local')
            && $request->is('telescope', 'telescope/*');

        $scriptSrc = "script-src 'self' 'unsafe-inline'";
        $styleSrc = "style-src 'self' 'unsafe-inline'";
        $fontSrc = "font-src 'self' data:";

        if ($isLocalTelescope) {
            $scriptSrc .= " 'unsafe-eval'";
            $styleSrc .= " https://fonts.bunny.net";
            $fontSrc .= " https://fonts.bunny.net";
        }

        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "base-uri 'self'; " .
            "object-src 'none'; " .
            "frame-ancestors 'self'; " .
            "form-action 'self'; " .
            "img-src 'self' data: blob:; " .
            $fontSrc . "; " .
            "connect-src 'self'; " .
            $scriptSrc . "; " .
            $styleSrc
        );

        // Enable HSTS only in production over HTTPS.
        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000'
            );
        }

        return $response;
    }
}

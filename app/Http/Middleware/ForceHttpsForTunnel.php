<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsForTunnel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect if request is from tunneling service (Ngrok, LocalTunnel, etc)
        $isTunnel = $request->header('X-Forwarded-Proto') === 'https' ||
                    str_contains($request->header('Host', ''), 'ngrok') ||
                    str_contains($request->header('Host', ''), 'loca.lt') ||
                    str_contains($request->header('Host', ''), 'serveo.net');

        if ($isTunnel) {
            // Force HTTPS for asset URLs
            URL::forceScheme('https');

            // Set the root URL to the tunnel URL
            if ($request->header('Host')) {
                URL::forceRootUrl('https://'.$request->header('Host'));
            }
        }

        return $next($request);
    }
}

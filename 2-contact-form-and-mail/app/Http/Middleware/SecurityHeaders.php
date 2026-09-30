<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds standard security headers to every response.
 *
 * Register this GLOBALLY (see README-security-setup.md for the two
 * registration patterns depending on your Laravel version).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // --- Content-Security-Policy ---------------------------------
        // Start restrictive, then widen only for what you actually load.
        // 'self' covers your own domain. Add specific hosts for any CDN,
        // fonts, analytics, or embedded widgets (e.g. cdnjs, Google Fonts).
        //
        // img-src includes the msp-portal domain (config/portal.php,
        // env PORTAL_URL) — blog post featured images and inline body
        // images are hosted on the portal and loaded here by URL. Each
        // environment's .env sets PORTAL_URL to that tier's actual portal
        // domain (local -> local, dev -> dev, qa -> qa, live -> live).
        $portalUrl = config('portal.url');

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' blob: cdn.jsdelivr.net cdnjs.cloudflare.com unpkg.com js.zi-scripts.com ws-assets.zoominfo.com",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com cdnjs.cloudflare.com unpkg.com",
            "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com",
            "img-src 'self' data: images.unsplash.com portal.dev.i360sites.com i360-llc.com www.i360-llc.com".($portalUrl ? " {$portalUrl}" : ''),
            "connect-src 'self' js.zi-scripts.com ws-assets.zoominfo.com ws.zoominfo.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // --- HSTS -------------------------------------------------------
        // Only set this once you're 100% certain the site is always served
        // over HTTPS (Cloudways dev/staging with self-signed certs can break
        // if this leaks into non-prod). 1 year, include subdomains.
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // --- Standard hardening headers ---------------------------------
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=()'
        );

        // Legacy header, harmless to keep for older browsers.
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}

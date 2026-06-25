<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $this->fixHttpsBehindProxy();
    }

    /**
     * Force HTTPS asset URLs when running behind a TLS-terminating proxy
     * (Render, Cloudflare, Fly, etc.). Without this, Laravel generates
     * http:// asset URLs because php artisan serve sees the in-container
     * request as plain HTTP. Browsers block those as mixed active content
     * on an https:// page, leaving a blank screen.
     */
    private function fixHttpsBehindProxy(): void
    {
        $request = request();

        if (! $request instanceof Request) {
            return;
        }

        // Trust Render's edge so X-Forwarded-Proto / X-Forwarded-Host are
        // honoured by Symfony's request. '*' is fine here because we only
        // trust the forwarding headers — we still validate the request
        // body and never act on payload from those headers.
        if (config('app.env') === 'production') {
            $request->setTrustedProxies(
                ['127.0.0.1', '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '::1'],
                Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
            );

            if ($request->isSecure() || $request->headers->get('X-Forwarded-Proto') === 'https') {
                URL::forceScheme('https');
            }
        }
    }
}
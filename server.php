<?php

/**
 * Custom router for `php artisan serve` (PHP's built-in web server).
 *
 * Laravel checks for this file at the project root and uses it instead
 * of its built-in router. We use it to add `Cache-Control: no-cache`
 * headers to the Vite build assets under /build/assets/.
 *
 * Why: Render's free tier sleeps after 15 min of inactivity. When the
 * next request arrives, the origin can take 30+ seconds to wake up.
 * During that window Cloudflare may receive a 520 error page from
 * Render; if Cloudflare is allowed to cache responses, it will replay
 * the broken "error code: 520" body — which arrives with a JS content
 * type — to every subsequent visitor until the cache TTL expires,
 * leaving the Inertia app unable to mount.
 *
 * Vite hashes its asset filenames, so there is no benefit to caching
 * them at the CDN anyway; the URL is unique per build.
 */

$publicPath = __DIR__.'/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Match /build/assets/<anything> and serve the file with no-cache headers.
if (preg_match('#^/build/assets/(.+)$#', $uri, $m)) {
    $relative = $m[1];
    // Defence in depth: reject anything that escapes the assets dir.
    if (strpos($relative, '..') !== false) {
        return false;
    }
    $file = $publicPath.'/build/assets/'.$relative;
    if (is_file($file)) {
        // Pick a content-type from the extension. The Vite manifest only
        // ships .js, .css and .woff2 files, but cover the obvious cases.
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $types = [
            'js'   => 'application/javascript; charset=utf-8',
            'mjs'  => 'application/javascript; charset=utf-8',
            'css'  => 'text/css; charset=utf-8',
            'woff' => 'font/woff',
            'woff2'=> 'font/woff2',
            'ttf'  => 'font/ttf',
            'eot'  => 'application/vnd.ms-fontobject',
            'svg'  => 'image/svg+xml',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'ico'  => 'image/x-icon',
            'json' => 'application/json; charset=utf-8',
            'map'  => 'application/json; charset=utf-8',
        ];
        $type = $types[$ext] ?? 'application/octet-stream';

        header('Content-Type: '.$type);
        header('Content-Length: '.filesize($file));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        // Support If-Modified-Since so returning visitors still get 304s.
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $ims = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
            if ($ims !== false && $ims >= filemtime($file)) {
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT');
                http_response_code(304);
                return true;
            }
        }
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT');
        readfile($file);
        return true;
    }
    // File does not exist inside the build dir — let it fall through to
    // index.php so Laravel can return a proper 404 instead of the dev
    // server's default behaviour.
}

// Default Laravel behaviour for everything else.
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

$formattedDateTime = date('D M j H:i:s Y');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];
file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $publicPath.'/index.php';
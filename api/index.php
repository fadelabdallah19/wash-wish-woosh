<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);
$tmpPath = '/tmp';

// Create writable directories on cold start
$dirs = [
    $tmpPath . '/storage/framework/views',
    $tmpPath . '/storage/framework/cache',
    $tmpPath . '/storage/framework/cache/data',
    $tmpPath . '/storage/framework/sessions',
    $tmpPath . '/storage/logs',
    $basePath . '/bootstrap/cache',
    $tmpPath . '/database',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Serve static files
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$mimeTypes = [
    'css'  => 'text/css; charset=utf-8',
    'js'   => 'application/javascript; charset=utf-8',
    'mjs'  => 'application/javascript; charset=utf-8',
    'json' => 'application/json; charset=utf-8',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'svg'  => 'image/svg+xml',
    'ico'  => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2'=> 'font/woff2',
    'ttf'  => 'font/ttf',
    'webp' => 'image/webp',
];

if ($requestUri !== '/' && $requestUri !== false) {
    $ext = strtolower(pathinfo($requestUri, PATHINFO_EXTENSION));

    if (isset($mimeTypes[$ext])) {
        $staticFile = $basePath . '/public' . $requestUri;
        $altFile = $basePath . $requestUri;

        $file = null;
        if (is_file($staticFile)) {
            $file = $staticFile;
        } elseif (is_file($altFile)) {
            $file = $altFile;
        }

        if ($file !== null) {
            http_response_code(200);
            header('Content-Type: ' . $mimeTypes[$ext]);
            header('Cache-Control: public, max-age=31536000, immutable');
            header('Access-Control-Allow-Origin: *');
            echo file_get_contents($file);
            exit;
        }
    }
}

// Maintenance mode
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';

// Create SQLite database and run migrations on cold start
$dbPath = $tmpPath . '/database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    $app->make('Illuminate\Contracts\Console\Kernel')->call('migrate', ['--force' => true]);
}

$app->handleRequest(Request::capture());

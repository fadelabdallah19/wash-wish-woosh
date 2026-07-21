<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);
$tmpPath = '/tmp';

// Redirect writable paths to /tmp on Vercel
if (!file_exists($tmpPath . '/storage')) {
    mkdir($tmpPath . '/storage', 0755, true);
    mkdir($tmpPath . '/storage/framework', 0755, true);
    mkdir($tmpPath . '/storage/framework/views', 0755, true);
    mkdir($tmpPath . '/storage/framework/cache', 0755, true);
    mkdir($tmpPath . '/storage/framework/cache/data', 0755, true);
    mkdir($tmpPath . '/storage/framework/sessions', 0755, true);
    mkdir($tmpPath . '/storage/logs', 0755, true);
}

// Maintenance mode
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';

// Override storage path for Vercel
$app->useStoragePath($tmpPath . '/storage');

// Set compiled view path to /tmp
$app['config']['view.compiled'] = $tmpPath . '/storage/framework/views';

$app->handleRequest(Request::capture());
